<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Sucursal;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Mail\TurnoReasignadoMail;
use App\Services\AbsenceManagerService;

class TurnoController extends Controller
{
    public function create(Request $request)
    {
        $sucursales = Sucursal::where('activo', 1)->get();
        $servicios = Servicio::where('activo', 1)->get();

        $servicioSeleccionado = null;
        if ($request->filled('servicio_id')) {
            $servicioSeleccionado = Servicio::find($request->servicio_id);
        }

        return view('usuario.turnos.create', compact('sucursales', 'servicios', 'servicioSeleccionado'));
    }

    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->estaPenalizado()) {
            $fechaFinPenalizacion = Carbon::parse(auth()->user()->penalizado_hasta)->format('d/m/Y H:i');
            return back()
                ->withInput()
                ->with('error', 'Tu cuenta se encuentra suspendida temporalmente para turnos web debido a reiteradas ausencias (hasta el ' . $fechaFinPenalizacion . ' hs). Comunícate con el local.');
        }

        $request->validate([
            'servicios'   => 'required|array|min:1',
            'servicios.*' => 'exists:servicios,id',
            'barbero_id'  => 'required|exists:usuarios,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora'        => ['required', function ($attribute, $value, $fail) use ($request) {
                $fechaHora = Carbon::parse($request->fecha . ' ' . $value);

                if ($fechaHora->isPast()) {
                    $fail('La hora seleccionada ya pasó.');
                    return;
                }

                $diferencia = now()->diffInMinutes($fechaHora);

                if ($diferencia < 30) {
                    $fail('El turno debe reservarse con al menos 30 minutos de anticipación.');
                    return;
                }

                $minutos = (int) $fechaHora->format('i');
                if ($minutos % 15 !== 0) {
                    $fail('Los turnos deben programarse en intervalos exactos de 15 minutos (ej: :00, :15, :30, :45).');
                }
            }],
        ]);

        $serviciosSeleccionados = Servicio::whereIn('id', $request->servicios)->get();
        $duracionTotal = $serviciosSeleccionados->sum('duracion');
        $precioTotal = $serviciosSeleccionados->sum('precio_actual');
        $servicioPrincipalId = $request->servicios[0];

        if (
            $this->existeConflictoHorario(
                $request->barbero_id,
                $request->sucursal_id,
                $request->fecha,
                $request->hora,
                $duracionTotal
            )
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'hora' => 'Ese barbero ya tiene un turno reservado dentro de ese rango horario.'
                ]);
        }

        session([
            'reserva' => [
                'cliente_id'       => auth()->id(),
                'barbero_id'       => $request->barbero_id,
                'servicio_id'      => $servicioPrincipalId,
                'servicios'        => $request->servicios,
                'sucursal_id'      => $request->sucursal_id,
                'fecha'            => $request->fecha,
                'hora'             => $request->hora,
                'precio_historico' => $precioTotal,
                'origen'           => 'web',
            ]
        ]);

        return redirect()->route('pago.checkout');
    }

    public function edit($id)
    {
        $turno = Turno::findOrFail($id);

        if (auth()->user()->rol->nombre !== 'cliente' || $turno->cliente_id !== auth()->id()) {
            return redirect()->route('usuario.turnos')->with('error', 'No tenés permiso para editar este turno.');
        }

        if ($turno->estado !== 'confirmado') {
            return redirect()->route('usuario.turnos')->with('error', 'Solo podés reprogramar turnos confirmados.');
        }

        if (($turno->reprogramaciones_count ?? 0) >= 2) {
            return redirect()->route('usuario.turnos')->with('error', 'Ya alcanzaste el límite máximo de 2 reprogramaciones para este turno.');
        }

        $horaTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora);
        $minutosRestantes = now()->diffInMinutes($horaTurno, false);

        if ($minutosRestantes < 120) {
            return redirect()->route('usuario.turnos')->with('error', 'La reprogramación solo se permite con al menos 2 horas de anticipación.');
        }

        $servicios = Servicio::all();
        $sucursales = Sucursal::all();
        $barberos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })->get();

        return view('usuario.turnos.edit', compact('turno', 'servicios', 'sucursales', 'barberos'));
    }

    public function update(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);

        if (auth()->user()->rol->nombre === 'cliente') {
            if ($turno->cliente_id !== auth()->id()) {
                abort(403, 'No tenés permiso para editar este turno.');
            }

            if (($turno->reprogramaciones_count ?? 0) >= 2) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Ya alcanzaste el límite máximo de 2 reprogramaciones para este turno.'], 422);
                }
                return redirect()->route('usuario.turnos')->with('error', 'Ya alcanzaste el límite máximo de 2 reprogramaciones para este turno.');
            }

            $request->validate([
                'servicios'   => 'required|array|min:1',
                'servicios.*' => 'exists:servicios,id',
                'barbero_id'  => 'required|exists:usuarios,id',
                'sucursal_id' => 'required|exists:sucursales,id',
                'fecha'       => 'required|date|after_or_equal:today',
                'hora'        => ['required', function ($attribute, $value, $fail) use ($request) {
                    $fechaHora = Carbon::parse($request->fecha . ' ' . $value);

                    if ($fechaHora->isPast()) {
                        $fail('La hora seleccionada ya pasó.');
                        return;
                    }

                    $diferencia = now()->diffInMinutes($fechaHora, false);

                    if ($diferencia < 120) {
                        $fail('La reprogramación debe realizarse con al menos 2 horas de anticipación.');
                    }
                }],
            ]);

            $fechaCreacionOriginal = Carbon::parse($turno->created_at)->startOfDay();
            $nuevaFechaSeleccionada = Carbon::parse($request->fecha)->startOfDay();

            if ($fechaCreacionOriginal->diffInDays($nuevaFechaSeleccionada) > 30) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['errors' => ['fecha' => ['La nueva fecha no puede superar los 30 días posteriores a la fecha original de compra.']]], 422);
                }
                return redirect()->back()->withInput()->withErrors([
                    'fecha' => 'La nueva fecha no puede superar los 30 días posteriores a la fecha original de compra.'
                ]);
            }

            $serviciosSeleccionados = Servicio::whereIn('id', $request->servicios)->get();
            $duracionTotal = $serviciosSeleccionados->sum('duracion');
            $precioTotal = $serviciosSeleccionados->sum('precio_actual');
            $servicioPrincipalId = $request->servicios[0];

            if (
                $this->existeConflictoHorario(
                    $request->barbero_id,
                    $request->sucursal_id,
                    $request->fecha,
                    $request->hora,
                    $duracionTotal,
                    $turno->id
                )
            ) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['errors' => ['hora' => ['Ese barbero ya tiene un turno reservado en ese rango de horario.']]], 422);
                }
                return redirect()->back()->withInput()->withErrors([
                    'hora' => 'Ese barbero ya tiene un turno reservado en ese rango de horario.'
                ]);
            }

            $turno->update([
                'servicio_id'            => $servicioPrincipalId,
                'barbero_id'             => $request->barbero_id,
                'sucursal_id'            => $request->sucursal_id,
                'fecha'                  => $request->fecha,
                'hora'                   => $request->hora,
                'precio_historico'       => $precioTotal,
                'reprogramaciones_count' => ($turno->reprogramaciones_count ?? 0) + 1,
            ]);

            $turno->servicios()->sync($request->servicios);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Turno reprogramado correctamente.']);
            }

            return redirect()->route('usuario.turnos')->with('success', 'Turno reprogramado correctamente.');
        }

        $request->validate([
            'estado' => 'required|in:confirmado,completado,cancelado,ausente',
        ]);

        $servicio = Servicio::findOrFail($turno->servicio_id);

        try {
            \DB::transaction(function () use ($request, $servicio, $turno) {
                if (
                    $request->estado === 'confirmado' &&
                    $this->existeConflictoHorario(
                        $turno->barbero_id,
                        $turno->sucursal_id,
                        $turno->fecha,
                        $turno->hora,
                        $servicio->duracion,
                        $turno->id
                    )
                ) {
                    throw new \Exception('CHOQUE_HORARIO');
                }

                $turno->estado = $request->estado;
                $turno->save();
            });

        } catch (\Exception $e) {
            if ($e->getMessage() === 'CHOQUE_HORARIO') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'No se pudo actualizar: el horario ya está ocupado.'], 422);
                }
                return redirect()->back()->with('error', 'No se pudo actualizar: el horario ya está ocupado.');
            }
            throw $e;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'El turno fue actualizado correctamente.']);
        }

        return redirect()->back()->with('success', 'El turno fue actualizado correctamente.');
    }

    public function cancelarCliente($id)
    {
        $turno = Turno::findOrFail($id);

        if ($turno->cliente_id !== auth()->id() && auth()->user()->rol->nombre !== 'admin') {
            abort(403, 'No tenés permiso para cancelar este turno.');
        }

        if ($turno->estado === 'cancelado') {
            return redirect()->route('usuario.turnos')->with('error', 'El turno ya se encuentra cancelado.');
        }

        $horaTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora);
        $minutosRestantes = now()->diffInMinutes($horaTurno, false);

        if ($minutosRestantes < 120 && auth()->user()->rol->nombre === 'cliente') {
            return redirect()->route('usuario.turnos')->with('error', 'El turno está a menos de 2 horas de su inicio y se encuentra bloqueado para autogestión. Comunícate con la barbería.');
        }

        $pago = Pago::where('turno_id', $turno->id)->where('estado', 'pagado')->first();
        $mensajeExito = 'Turno cancelado correctamente.';

        // REGLA DE HIERRO (Opción A): Solo aplica reembolso si falta >= 24 hs Y NUNCA fue reprogramado
        $aplicaReembolso = ($minutosRestantes >= 1440) && (($turno->reprogramaciones_count ?? 0) === 0);

        if ($aplicaReembolso && $pago && $pago->payment_id_mp) {
            $accessToken = config('services.mercadopago.access_token');

            if (!$accessToken) {
                return redirect()->route('usuario.turnos')->with('error', 'Error de configuración: no se encontró el Access Token de Mercado Pago.');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post("https://api.mercadopago.com/v1/payments/{$pago->payment_id_mp}/refunds");

            // Valida éxito en API o permite avanzar si estás testeando en entorno local/sandbox
            if ($response->successful() || config('app.env') === 'local') {
                $pago->update(['estado' => 'reembolsado']);
                $mensajeExito = 'Turno cancelado y el importe fue reembolsado a través de Mercado Pago.';
            } else {
                Log::error('Error de Reembolso Mercado Pago:', [
                    'turno_id' => $turno->id,
                    'payment_id_mp' => $pago->payment_id_mp,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return redirect()->route('usuario.turnos')->with('error', 'No se pudo procesar el reembolso en Mercado Pago. Intente nuevamente.');
            }
        } 
        else if ($minutosRestantes >= 120) {
            $mensajeExito = 'Turno cancelado correctamente. (No aplica reembolso por política de reprogramación previa o por cercanía a la fecha).';
        }

        // Actualización estricta de estado (borrado lógico)
        $turno->update([
            'estado' => 'cancelado'
        ]);

        return redirect()->route('usuario.turnos')->with('success', $mensajeExito);
    }
    public function misTurnosUsuario()
    {
        $usuarioId = auth()->id();
        $hoyString = Carbon::now()->toDateString();

        $todosLosTurnos = Turno::with(['barbero', 'servicio', 'servicios', 'sucursal'])
            ->where('cliente_id', $usuarioId)
            ->get();

        $turnosProximos = $todosLosTurnos->filter(function ($t) use ($hoyString) {
            $fechaTurno = Carbon::parse($t->fecha)->toDateString();
            $yaPaso = Carbon::parse($t->fecha . ' ' . $t->hora)->isPast();
            return !$yaPaso && $fechaTurno >= $hoyString && in_array($t->estado, ['pendiente', 'confirmado']);
        })->sortBy([
            ['fecha', 'asc'],
            ['hora', 'asc']
        ]);

        $turnosPasados = $todosLosTurnos->filter(function ($t) use ($hoyString) {
            $fechaTurno = Carbon::parse($t->fecha)->toDateString();
            $yaPaso = Carbon::parse($t->fecha . ' ' . $t->hora)->isPast();
            return $yaPaso || in_array($t->estado, ['completado', 'cancelado', 'vencido', 'ausente']);
        })->sortByDesc('fecha');

        $turnos = $turnosProximos->concat($turnosPasados);

        return view('usuario.turnos.index', compact('turnos'));
    }

    public function index(Request $request)
    {
        $query = Turno::with(['cliente', 'barbero', 'servicio', 'servicios', 'sucursal', 'pago'])
                     ->orderBy('fecha', 'asc')
                     ->orderBy('hora', 'asc');

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
        }

        if ($request->filled('barbero_id')) {
            $query->where('barbero_id', $request->barbero_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo_turno')) {
            if ($request->tipo_turno === 'online') {
                $query->whereHas('pago', function($q) {
                    $q->where('metodo', 'mercado_pago');
                });
            } elseif ($request->tipo_turno === 'desktop') {
                $query->where(function($subQ) {
                    $subQ->whereDoesntHave('pago')
                         ->orWhereHas('pago', function($q) {
                             $q->where('metodo', '!=', 'mercado_pago');
                         });
                });
            }
        }

        if ($request->filled('range')) {
            $today = now()->startOfDay();
            switch ($request->range) {
                case 'hoy':
                    $query->whereDate('fecha', $today);
                    break;
                case '3':
                    $query->whereBetween('fecha', [$today->copy()->subDays(2), $today]);
                    break;
                case '7':
                    $query->whereBetween('fecha', [$today->copy()->subDays(6), $today]);
                    break;
                case '30':
                    $query->whereBetween('fecha', [$today->copy()->subDays(29), $today]);
                    break;
            }
        }

        $todosLosTurnos = $query->get();
        $hoyString = now()->toDateString();

        $vigentes = $todosLosTurnos->filter(function($t) use ($hoyString) {
            $fecha = Carbon::parse($t->fecha)->toDateString();
            return $fecha >= $hoyString && !in_array($t->estado, ['completado', 'cancelado', 'vencido', 'ausente']);
        })->sortBy([
            ['fecha', 'asc'],
            ['hora', 'asc']
        ]);

        $historial = $todosLosTurnos->filter(function($t) use ($hoyString) {
            $fecha = Carbon::parse($t->fecha)->toDateString();
            return $fecha < $hoyString || in_array($t->estado, ['completado', 'cancelado', 'vencido', 'ausente']);
        })->sortByDesc('fecha');

        $turnos = $vigentes->concat($historial);

        $sucursales = Sucursal::all();
        
        $barberos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })->get(['id', 'nombre', 'sucursal_id']);

        $clientes = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'cliente');
        })->get(['id', 'nombre', 'email']);

        $servicios = Servicio::where('activo', 1)->get();

        return view('admin.turnos.index', compact('turnos', 'sucursales', 'barberos', 'clientes', 'servicios'));
    }

    public function getBarberosPorSucursal($sucursalId)
    {
        $barberos = Usuario::with(['turnoTrabajo', 'rol'])
            ->where('sucursal_id', $sucursalId)
            ->get();

        $barberosValidos = $barberos->filter(function($b) {
            return isset($b->turnoTrabajo) && !empty($b->turnoTrabajo->hora_inicio);
        })->values();

        return response()->json($barberosValidos);
    }

    private function existeConflictoHorario(
        $barberoId,
        $sucursalId,
        $fecha,
        $hora,
        $duracion,
        $turnoExcluir = null
    ) {
        $inicioNuevo = Carbon::parse($fecha . ' ' . $hora);
        $finNuevo = $inicioNuevo->copy()->addMinutes($duracion);

        $query = Turno::with(['servicio', 'servicios'])
            ->where('barbero_id', $barberoId)
            ->where('sucursal_id', $sucursalId)
            ->where('fecha', $fecha)
            ->where('estado', 'confirmado');

        if ($turnoExcluir) {
            $query->where('id', '!=', $turnoExcluir);
        }

        $turnosExistentes = $query->get();

        foreach ($turnosExistentes as $turno) {
            $inicioExistente = Carbon::parse(
                $turno->fecha . ' ' . $turno->hora
            );

            $duracionExistente = $turno->servicios->isNotEmpty() 
                ? $turno->servicios->sum('duracion') 
                : ($turno->servicio->duracion ?? 30);

            $finExistente = $inicioExistente->copy()->addMinutes($duracionExistente);

            if (
                $inicioNuevo < $finExistente &&
                $finNuevo > $inicioExistente
            ) {
                return true;
            }
        }

        return false;
    }

    public function getHorariosOcupados(Request $request)
    {
        $barberoId = $request->query('barbero_id');
        $sucursalId = $request->query('sucursal_id');
        $fecha = $request->query('fecha');
        $turnoExcluir = $request->query('turno_id');

        if (!$barberoId || !$sucursalId || !$fecha) {
            return response()->json([]);
        }

        $query = Turno::with(['servicio', 'servicios'])
            ->where('barbero_id', $barberoId)
            ->where('sucursal_id', $sucursalId)
            ->where('fecha', $fecha)
            ->where('estado', 'confirmado');

        if ($turnoExcluir) {
            $query->where('id', '!=', $turnoExcluir);
        }

        $turnos = $query->get();

        $rangosOcupados = $turnos->map(function ($t) {
            $inicio = Carbon::parse($t->fecha . ' ' . $t->hora);
            $duracion = $t->servicios->isNotEmpty() ? $t->servicios->sum('duracion') : ($t->servicio->duracion ?? 30);
            $fin = $inicio->copy()->addMinutes($duracion);

            return [
                'inicio' => $inicio->format('H:i'),
                'fin' => $fin->format('H:i')
            ];
        });

        return response()->json($rangosOcupados);
    }

    public function getHorariosDisponibles(Request $request)
    {
        $barberoId = $request->query('barbero_id');
        $sucursalId = $request->query('sucursal_id');
        $fecha = $request->query('fecha');
        $duracionTotal = (int) $request->query('duracion_total', 30);
        $turnoExcluir = $request->query('excluir_turno_id') ?? $request->query('turno_id');

        if (!$barberoId || !$sucursalId || !$fecha || !$duracionTotal) {
            return response()->json([], 400);
        }

        $barbero = Usuario::with('turnoTrabajo')->find($barberoId);
        if (!isset($barbero->turnoTrabajo)) {
            return response()->json([]);
        }

        $hInicioBarbero = Carbon::parse($fecha . ' ' . $barbero->turnoTrabajo->hora_inicio);
        $hFinBarbero = Carbon::parse($fecha . ' ' . $barbero->turnoTrabajo->hora_fin);

        $query = Turno::with(['servicio', 'servicios'])
            ->where('barbero_id', $barberoId)
            ->where('sucursal_id', $sucursalId)
            ->where('fecha', $fecha)
            ->where('estado', 'confirmado');

        if ($turnoExcluir) {
            $query->where('id', '!=', $turnoExcluir);
        }

        $turnosOcupados = $query->get();

        $slotsDisponibles = [];
        $ahora = Carbon::now();
        $esHoy = Carbon::parse($fecha)->isToday();

        $slotActual = $hInicioBarbero->copy();

        while ($slotActual->copy()->addMinutes($duracionTotal)->lte($hFinBarbero)) {
            $finSlot = $slotActual->copy()->addMinutes($duracionTotal);
            $horaStr = $slotActual->format('H:i');

            if ($esHoy && $slotActual->copy()->lt($ahora->copy()->addMinutes(30))) {
                $slotActual->addMinutes(15);
                continue;
            }

            $hayConflicto = false;
            foreach ($turnosOcupados as $t) {
                $inicioExistente = Carbon::parse($t->fecha . ' ' . $t->hora);
                $duracionExistente = $t->servicios->isNotEmpty() ? $t->servicios->sum('duracion') : ($t->servicio->duracion ?? 30);
                $finExistente = $inicioExistente->copy()->addMinutes($duracionExistente);

                if ($slotActual < $finExistente && $finSlot > $inicioExistente) {
                    $hayConflicto = true;
                    break;
                }
            }

            if (!$hayConflicto) {
                $slotsDisponibles[] = [
                    'hora' => $horaStr,
                    'formato' => $horaStr . ' hs'
                ];
            }

            $slotActual->addMinutes(15);
        }

        return response()->json($slotsDisponibles);
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'tipo_cliente'          => 'required|in:registrado,manual',
            'cliente_id'            => 'required_if:tipo_cliente,registrado|nullable|exists:usuarios,id',
            'cliente_nombre_manual' => 'nullable|string|max:255',
            'servicios'             => 'required|array|min:1',
            'servicios.*'           => 'exists:servicios,id',
            'barbero_id'            => 'required|exists:usuarios,id',
            'sucursal_id'           => 'required|exists:sucursales,id',
            'fecha'                 => 'required|date|after_or_equal:today',
            'hora'                  => 'required',
            'metodo_pago'           => 'required|string',
        ]);

        $serviciosSeleccionados = Servicio::whereIn('id', $request->servicios)->get();
        $duracionTotal = $serviciosSeleccionados->sum('duracion');
        $precioTotal = $serviciosSeleccionados->sum('precio_actual');
        $servicioPrincipalId = $request->servicios[0];

        try {
            $turno = \DB::transaction(function () use ($request, $duracionTotal, $precioTotal, $servicioPrincipalId) {
                
                if (
                    $this->existeConflictoHorario(
                        $request->barbero_id,
                        $request->sucursal_id,
                        $request->fecha,
                        $request->hora,
                        $duracionTotal
                    )
                ) {
                    throw new \Exception('CHOQUE_HORARIO');
                }

                $clienteId = null;
                $clienteNombreManual = null;

                if ($request->tipo_cliente === 'registrado') {
                    $clienteId = $request->cliente_id;
                } else {
                    $clienteOcasional = Usuario::firstOrCreate(
                        ['email' => 'mostrador@barberia.com'],
                        [
                            'nombre'   => 'Cliente Ocasional',
                            'password' => bcrypt('cliente_mostrador_123'),
                            'rol_id'   => 3,
                        ]
                    );
                    $clienteId = $clienteOcasional->id;

                    $clienteNombreManual = $request->filled('cliente_nombre_manual') 
                        ? $request->cliente_nombre_manual 
                        : 'Cliente Ocasional';
                }

                $nuevoTurno = Turno::create([
                    'cliente_id'            => $clienteId,
                    'cliente_nombre_manual' => $clienteNombreManual,
                    'barbero_id'            => $request->barbero_id,
                    'servicio_id'           => $servicioPrincipalId,
                    'sucursal_id'           => $request->sucursal_id,
                    'fecha'                 => $request->fecha,
                    'hora'                  => $request->hora,
                    'estado'                => 'confirmado',
                    'precio_historico'      => $precioTotal,
                ]);

                $nuevoTurno->servicios()->sync($request->servicios);

                Pago::create([
                    'turno_id'    => $nuevoTurno->id,
                    'monto'       => $precioTotal,
                    'metodo'      => $request->metodo_pago,
                    'estado'      => 'pagado', 
                ]);

                return $nuevoTurno;
            });

        } catch (\Exception $e) {
            if ($e->getMessage() === 'CHOQUE_HORARIO') {
                return back()
                    ->withInput()
                    ->withErrors([
                        'hora' => '¡Alguien fue más rápido! El horario de las ' . $request->hora . ' hs acaba de ser reservado hace unos instantes. Por favor, selecciona otro.'
                    ])
                    ->with('error', 'El turno seleccionado acaba de ser ocupado en una interacción paralela. Por favor, elija otro horario.');
            }

            throw $e;
        }

        if (auth()->user() && auth()->user()->rol && auth()->user()->rol->nombre === 'barbero') {
            return redirect()->route('barbero.turnos')->with('success', 'Turno presencial registrado con éxito.');
        }

        return redirect()->route('admin.turnos.index')->with('success', '¡Turno manual otorgado correctamente!');
    }

    public function updateAdmin(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);

        $request->validate([
            'servicios'             => 'required|array|min:1',
            'servicios.*'           => 'exists:servicios,id',
            'barbero_id'            => 'required|exists:usuarios,id',
            'sucursal_id'           => 'required|exists:sucursales,id',
            'fecha'                 => 'required|date',
            'hora'                  => 'required',
            'estado'                => 'required|in:pendiente,confirmado,completado,cancelado,ausente',
            'cliente_nombre_manual' => 'nullable|string|max:255',
        ]);

        $serviciosSeleccionados = Servicio::whereIn('id', $request->servicios)->get();
        $duracionTotal = $serviciosSeleccionados->sum('duracion');
        $precioTotal = $serviciosSeleccionados->sum('precio_actual');
        $servicioPrincipalId = $request->servicios[0];

        try {
            \DB::transaction(function () use ($request, $duracionTotal, $precioTotal, $servicioPrincipalId, $turno) {
                if (
                    $this->existeConflictoHorario(
                        $request->barbero_id,
                        $request->sucursal_id,
                        $request->fecha,
                        $request->hora,
                        $duracionTotal,
                        $turno->id
                    )
                ) {
                    throw new \Exception('CHOQUE_HORARIO');
                }

                $datosActualizar = [
                    'barbero_id'        => $request->barbero_id,
                    'servicio_id'       => $servicioPrincipalId,
                    'sucursal_id'       => $request->sucursal_id,
                    'fecha'             => $request->fecha,
                    'hora'              => $request->hora,
                    'estado'            => $request->estado,
                    'precio_historico'  => $precioTotal,
                ];

                if (!empty($turno->cliente_nombre_manual)) {
                    $datosActualizar['cliente_nombre_manual'] = $request->filled('cliente_nombre_manual')
                        ? $request->cliente_nombre_manual
                        : 'Cliente Ocasional';
                }

                $turno->update($datosActualizar);
                $turno->servicios()->sync($request->servicios);
            });

        } catch (\Exception $e) {
            if ($e->getMessage() === 'CHOQUE_HORARIO') {
                return redirect()->back()->withInput()->withErrors(['hora' => 'El horario seleccionado entra en conflicto con otro turno existente.'])
                    ->with('error', 'No se pudo actualizar: el horario ya está ocupado.');
            }
            throw $e;
        }

        return redirect()->route('admin.turnos.index')->with('success', '¡Turno actualizado correctamente!');
    }

    public function cancelarAdmin($id)
    {
        $turno = Turno::findOrFail($id);
        
        $turno->update([
            'estado' => 'cancelado'
        ]);

        return redirect()->route('admin.turnos.index')->with('success', '¡Turno cancelado correctamente!');
    }

    public function completado($id)
    {
        $turno = Turno::findOrFail($id);
        $turno->load(['servicio', 'servicios', 'pago']);

        $inicioTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora);
        $duracionMinutos = $turno->servicios->isNotEmpty() 
            ? $turno->servicios->sum('duracion') 
            : ($turno->servicio->duracion ?? 30);

        $finTurno = (clone $inicioTurno)->addMinutes($duracionMinutos);
        $limiteTurno = (clone $finTurno)->addHours(3);
        $ahora = Carbon::now();

        if ($ahora->lessThan($inicioTurno)) {
            return redirect()->back()->with('error', 'No se puede completar el turno antes de su hora de inicio (' . $inicioTurno->format('H:i') . ' hs).');
        }

        if ($ahora->greaterThan($limiteTurno)) {
            return redirect()->back()->with('error', 'El plazo para completar este turno expiró.');
        }

        $turno->update([
            'estado' => 'completado'
        ]);

        $mensaje = $turno->esMostrador() 
            ? '¡Turno de mostrador completado con éxito! Impactado en caja.' 
            : '¡Turno web completado con éxito! Se habilitará la opción de calificación para el cliente.';

        return redirect()->back()->with('success', $mensaje);
    }

    public function ausente($id)
    {
        $turno = Turno::findOrFail($id);
        $inicioTurno = Carbon::parse($turno->fecha . ' ' . $turno->hora);
        $ahora = Carbon::now();

        if ($ahora->lessThan($inicioTurno)) {
            return redirect()->back()->with('error', 'No se puede marcar como ausente un turno antes de su hora de inicio.');
        }

        $turno->update([
            'estado' => 'ausente'
        ]);

        if ($turno->esPagoOnline() && $turno->cliente_id) {
            $cliente = $turno->cliente;
            if ($cliente) {
                $cliente->increment('ausencias_online_count');

                if ($cliente->ausencias_online_count >= 3) {
                    $cliente->update([
                        'penalizado_hasta' => now()->addDays(15),
                        'ausencias_online_count' => 0
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'El turno fue marcado como ausente (No-Show) y se actualizó el registro de faltas del cliente.');
    }

    public function catalogoServicios(Request $request)
    {
        $tipoId = $request->input('tipo');
        $tiposExcluidos = $request->input('excluir_tipos', []);

        $query = Servicio::where('activo', 1)->with('tipoServicio');

        if (!empty($tiposExcluidos)) {
            $query->whereNotIn('tipo_servicio_id', $tiposExcluidos);
        }

        $servicios = $query->get();

        $serviciosAgrupados = $servicios->groupBy(function($servicio) {
            return $servicio->tipoServicio->nombre ?? 'Servicios Generales';
        });

        return view('usuario.turnos.catalogo', compact('serviciosAgrupados', 'tipoId'));
    }

    public function cancelarEmergencia(Request $request, $id)
    {
        $request->validate([
            'motivo_cancelacion_emergencia' => 'required|string|min:5|max:500',
        ], [
            'motivo_cancelacion_emergencia.required' => 'Debes ingresar obligatoriamente un motivo de fuerza mayor para la cancelación de emergencia.'
        ]);

        $turno = Turno::findOrFail($id);

        if ($turno->estado === 'cancelado') {
            return redirect()->back()->with('error', 'El turno ya se encuentra cancelado.');
        }

        $pago = Pago::where('turno_id', $turno->id)->where('estado', 'pagado')->first();
        $mensajeExito = 'Turno cancelado por emergencia correctamente.';

        if ($pago && $pago->payment_id_mp) {
            $accessToken = config('services.mercadopago.access_token');

            if (!$accessToken) {
                return redirect()->route('admin.turnos.index')->with('error', 'Error de configuración: no se encontró el Access Token de Mercado Pago para el reembolso.');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post("https://api.mercadopago.com/v1/payments/{$pago->payment_id_mp}/refunds");

            if ($response->successful()) {
                $pago->update(['estado' => 'reembolsado']);
                $mensajeExito = 'Turno cancelado por emergencia. El importe total fue reembolsado a través de Mercado Pago y se guardó la auditoría.';
            } else {
                Log::error('Error de Reembolso de Emergencia Mercado Pago:', [
                    'turno_id' => $turno->id,
                    'payment_id_mp' => $pago->payment_id_mp,
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return redirect()->route('admin.turnos.index')->with('error', 'No se pudo procesar el reembolso automático en Mercado Pago. Intente nuevamente.');
            }
        }

        $turno->update([
            'estado' => 'cancelado',
            'motivo_cancelacion_emergencia' => $request->motivo_cancelacion_emergencia,
        ]);

        return redirect()->route('admin.turnos.index')->with('success', $mensajeExito);
    }

public function procesarInasistenciaAdmin(Request $request, AbsenceManagerService $absenceService)
{
    $request->validate([
        'turnos' => 'required|array',
    ]);

    // Delegamos toda la lógica de los 4 niveles de rescate al servicio
    $absenceService->procesarInasistenciaBarbero($request->input('turnos', []));

    return redirect()->back()->with('success', 'Inasistencias procesadas correctamente bajo el protocolo de rescate.');
}
}