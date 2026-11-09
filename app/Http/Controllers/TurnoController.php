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
    /**
     * Muestra la vista de reserva de turnos para el cliente.
     * Filtra y carga únicamente las sucursales que tengan barberos o admins activos con horarios configurados,
     * además del catálogo de servicios disponibles.
     */
    public function create(Request $request)
    {
        // Obtenemos los IDs de sucursales que tienen al menos un barbero/admin activo con horario de trabajo
        $sucursalIdsValidas = Usuario::whereIn('rol_id', [1, 2])
            ->where('activo', 1)
            ->whereNotNull('sucursal_id')
            ->whereHas('turnoTrabajo', function($q) {
                $q->whereNotNull('hora_inicio');
            })
            ->pluck('sucursal_id')
            ->unique();

        // Filtramos únicamente las sucursales activas que tengan personal operativo
        $sucursales = Sucursal::where('activo', 1)
            ->whereIn('id', $sucursalIdsValidas)
            ->get();

        $servicios = Servicio::where('activo', 1)->get();

        // Si el cliente viene seleccionando un servicio desde el catálogo, lo buscamos
        $servicioSeleccionado = null;
        if ($request->filled('servicio_id')) {
            $servicioSeleccionado = Servicio::find($request->servicio_id);
        }

        return view('usuario.turnos.create', compact('sucursales', 'servicios', 'servicioSeleccionado'));
    }

    /**
     * Valida e inicia la reserva de un turno web solicitado por un cliente.
     * Verifica penalizaciones por ausencias, solapamiento de horarios y reserva en sesión antes de ir al pago.
     */
    public function store(Request $request)
    {
        // 1. Bloqueo si el cliente se encuentra suspendido por reincidencia en ausencias (No-Show)
        if (auth()->check() && auth()->user()->estaPenalizado()) {
            $fechaFinPenalizacion = Carbon::parse(auth()->user()->penalizado_hasta)->format('d/m/Y H:i');
            return back()
                ->withInput()
                ->with('error', 'Tu cuenta se encuentra suspendida temporalmente para turnos web debido a reiteradas ausencias (hasta el ' . $fechaFinPenalizacion . ' hs). Comunícate con el local.');
        }

        // 2. Validaciones de la solicitud (servicios, barbero, sucursal, antelación mínima de 30 min e intervalos de 15 min)
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

        // 3. Verificación de choques de horario con otros turnos confirmados del mismo barbero
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

        // 4. Se guarda temporalmente en sesión para ser procesado por la pasarela de pago (Mercado Pago)
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

    /**
     * Carga el formulario para que el cliente pueda reprogramar su turno.
     * Valida permisos, límites de reprogramaciones (máx. 2) y que falten al menos 2 horas.
     */
    public function edit($id)
    {
        $turno = Turno::findOrFail($id);

        // Seguridad: solo el cliente dueño del turno puede acceder
        if (auth()->user()->rol->nombre !== 'cliente' || $turno->cliente_id !== auth()->id()) {
            return redirect()->route('usuario.turnos')->with('error', 'No tenés permiso para editar este turno.');
        }

        if ($turno->estado !== 'confirmado') {
            return redirect()->route('usuario.turnos')->with('error', 'Solo podés reprogramar turnos confirmados.');
        }

        // Límite de 2 reprogramaciones permitidas por turno
        if (($turno->reprogramaciones_count ?? 0) >= 2) {
            return redirect()->route('usuario.turnos')->with('error', 'Ya alcanzaste el límite máximo de 2 reprogramaciones para este turno.');
        }

        // Regla de antelación mínima de 2 horas para reprogramar
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

    /**
     * Procesa la actualización o reprogramación de un turno.
     * Maneja tanto la reprogramación por parte del cliente como los cambios de estado hechos por el panel de administración.
     */
    public function update(Request $request, $id)
    {
        $turno = Turno::findOrFail($id);

        // LÓGICA DE REPROGRAMACIÓN DEL CLIENTE
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

            // Regla de negocio: la nueva fecha no puede superar los 30 días desde la fecha de compra original
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

            // Validar que el nuevo horario no choque con otro turno
            if (
                $this->existeConflictoHorario(
                    $request->barbero_id,
                    $request->sucursal_id,
                    $request->fecha,
                    $request->hora,
                    $duracionTotal,
                    $turno->id // Excluimos el turno actual de la validación
                )
            ) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['errors' => ['hora' => ['Ese barbero ya tiene un turno reservado en ese rango de horario.']]], 422);
                }
                return redirect()->back()->withInput()->withErrors([
                    'hora' => 'Ese barbero ya tiene un turno reservado en ese rango de horario.'
                ]);
            }

            // Actualizar el turno e incrementar el contador de reprogramaciones
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

        // LÓGICA DE ACTUALIZACIÓN DE ESTADO DESDE PANEL ADMIN/BARBERO
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

    /**
     * Procesa la cancelación autogestionada por el cliente web.
     * Evalúa la regla de las 2hs de antelación y la devolución automática mediante API de Mercado Pago (si supera 24hs sin reprogramaciones previa).
     */
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

        // Bloqueo si intenta cancelar a menos de 2 horas del turno
        if ($minutosRestantes < 120 && auth()->user()->rol->nombre === 'cliente') {
            return redirect()->route('usuario.turnos')->with('error', 'El turno está a menos de 2 horas de su inicio y se encuentra bloqueado para autogestión. Comunícate con la barbería.');
        }

        $pago = Pago::where('turno_id', $turno->id)->where('estado', 'pagado')->first();
        $mensajeExito = 'Turno cancelado correctamente.';

        // Criterio de reembolso: mas de 24 hs (1440 min) de antelación Y sin reprogramaciones previas
        $aplicaReembolso = ($minutosRestantes >= 1440) && (($turno->reprogramaciones_count ?? 0) === 0);

        if ($aplicaReembolso && $pago && $pago->payment_id_mp) {
            $accessToken = config('services.mercadopago.access_token');

            if (!$accessToken) {
                return redirect()->route('usuario.turnos')->with('error', 'Error de configuración: no se encontró el Access Token de Mercado Pago.');
            }

            // Llamada a la API de Mercado Pago para efectuar el reembolso
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post("https://api.mercadopago.com/v1/payments/{$pago->payment_id_mp}/refunds");

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

        $turno->update([
            'estado' => 'cancelado'
        ]);

        return redirect()->route('usuario.turnos')->with('success', $mensajeExito);
    }

    /**
     * Obtiene el historial de turnos del cliente ordenados por vigentes/próximos y pasados/historial.
     */
    public function misTurnosUsuario()
    {
        $usuarioId = auth()->id();
        $hoyString = Carbon::now()->toDateString();

        $todosLosTurnos = Turno::with(['barbero', 'servicio', 'servicios', 'sucursal'])
            ->where('cliente_id', $usuarioId)
            ->get();

        // Filtrar turnos activos/próximos
        $turnosProximos = $todosLosTurnos->filter(function ($t) use ($hoyString) {
            $fechaTurno = Carbon::parse($t->fecha)->toDateString();
            $yaPaso = Carbon::parse($t->fecha . ' ' . $t->hora)->isPast();
            return !$yaPaso && $fechaTurno >= $hoyString && in_array($t->estado, ['pendiente', 'confirmado']);
        })->sortBy([
            ['fecha', 'asc'],
            ['hora', 'asc']
        ]);

        // Filtrar historial de turnos pasados o finalizados
        $turnosPasados = $todosLosTurnos->filter(function ($t) use ($hoyString) {
            $fechaTurno = Carbon::parse($t->fecha)->toDateString();
            $yaPaso = Carbon::parse($t->fecha . ' ' . $t->hora)->isPast();
            return $yaPaso || in_array($t->estado, ['completado', 'cancelado', 'vencido', 'ausente']);
        })->sortByDesc('fecha');

        $turnos = $turnosProximos->concat($turnosPasados);

        return view('usuario.turnos.index', compact('turnos'));
    }

    /**
     * Muestra la tabla general de gestión de turnos para Administradores y Barberos con filtros avanzados (sucursal, barbero, origen online/presencial, rango de fechas).
     */
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

        // Filtro por tipo de canal: Web (Mercado Pago) vs Presencial/Mostrador
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

        // Filtros por rango temporal
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

        // Cargar sucursales operativas para los selectores de los formularios
        $sucursalIdsValidas = Usuario::whereIn('rol_id', [1, 2])
            ->where('activo', 1)
            ->whereNotNull('sucursal_id')
            ->whereHas('turnoTrabajo', function($q) {
                $q->whereNotNull('hora_inicio');
            })
            ->pluck('sucursal_id')
            ->unique();

        $sucursales = Sucursal::where('activo', 1)
            ->whereIn('id', $sucursalIdsValidas)
            ->get();
        
        $barberos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })->get(['id', 'nombre', 'sucursal_id']);

        $clientes = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'cliente');
        })->get(['id', 'nombre', 'email']);

        $servicios = Servicio::where('activo', 1)->get();

        return view('admin.turnos.index', compact('turnos', 'sucursales', 'barberos', 'clientes', 'servicios'));
    }

    /**
     * Retorna una respuesta JSON con la lista de barberos pertenecientes a una sucursal específica que tengan horarios asignados.
     */
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

    /**
     * MÉRITO DE SEGURIDAD / ALGORITMO:
     * Comprueba si existe un solapamiento/conflicto de horarios para un barbero en una fecha y rango de duración determinado.
     * Devuelve true si hay choque, false si el rango está libre.
     */
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

            // Fórmula de intersección de rangos temporales: (InicioA < FinB) Y (FinA > InicioB)
            if (
                $inicioNuevo < $finExistente &&
                $finNuevo > $inicioExistente
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna en formato JSON los rangos de horas que un barbero ya tiene ocupados para ser bloqueados en la vista del calendario.
     */
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

    /**
     * Genera dinámicamente en tiempo real una lista de horarios (slots de 15 minutos) que están 100% libres 
     * dentro de la jornada laboral de un barbero y con espacio suficiente para la duración total del servicio.
     */
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

        // Recorrido de los bloques de tiempo dentro de la jornada laboral
        while ($slotActual->copy()->addMinutes($duracionTotal)->lte($hFinBarbero)) {
            $finSlot = $slotActual->copy()->addMinutes($duracionTotal);
            $horaStr = $slotActual->format('H:i');

            // Si es hoy, descarta los horarios que ya pasaron o que están a menos de 30 min
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

    /**
     * Permite a los Administradores o Barberos registrar manualmente un turno presencial / de mostrador.
     * Crea automáticamente el usuario "Cliente Ocasional" si no está registrado y liquida el pago inmediato.
     */
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
            // Transacción en base de datos para garantizar consistencia atómica entre Turno y Pago
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
                    // Si el cliente no está registrado en el sistema, lo asigna a un perfil comodín de mostrador
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

                // Registra el pago en caja (Efectivo, Débito, Transferencia, etc.)
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

    /**
     * Permite al Administrador modificar los datos completos de un turno (servicios, barbero, sucursal, fecha, hora y estado)
     * validando conflictos antes de persistir.
     */
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

    /**
     * Cancela directamente un turno desde el Panel de Administración (baja administrativa inmediata).
     */
    public function cancelarAdmin($id)
    {
        $turno = Turno::findOrFail($id);
        
        $turno->update([
            'estado' => 'cancelado'
        ]);

        return redirect()->route('admin.turnos.index')->with('success', '¡Turno cancelado correctamente!');
    }

    /**
     * Marca un turno como "completado" una vez atendido el cliente.
     * Valida que la atención haya comenzado y no supere un plazo máximo de 3 horas posteriores a su finalización.
     */
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

    /**
     * Registra la inasistencia (No-Show) de un cliente.
     * Si el turno fue pagado online, incrementa el contador de ausencias del cliente y aplica penalización por 15 días al llegar a 3 faltas.
     */
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

        // Contabilidad de faltas y sanción automática
        if ($turno->esPagoOnline() && $turno->cliente_id) {
            $cliente = $turno->cliente;
            if ($cliente) {
                $cliente->increment('ausencias_online_count');

                // Si llega a 3 ausencias online acumuladas, se lo suspende por 15 días
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

    /**
     * Devuelve la lista o catálogo de servicios agrupados por su tipo/categoría para la vista pública de selección.
     */
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

    /**
     * Cancela un turno por razones de emergencia / fuerza mayor (ej. ausencia imprevista de un barbero).
     * Requiere ingresar un motivo explícito, realiza el reembolso total por Mercado Pago si aplica y guarda el registro de auditoría.
     */
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

        // Si el turno fue abonado online vía Mercado Pago, se ejecuta el reembolso completo
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

        // Guarda el motivo explícito de la cancelación de emergencia
        $turno->update([
            'estado' => 'cancelado',
            'motivo_cancelacion_emergencia' => $request->motivo_cancelacion_emergencia,
        ]);

        return redirect()->route('admin.turnos.index')->with('success', $mensajeExito);
    }

    /**
     * Procesa la ausencia imprevista de un barbero delegando en el servicio AbsenceManagerService 
     * la reasignación de turnos o aviso a los clientes afectados mediante su protocolo de rescate.
     */
    public function procesarInasistenciaAdmin(Request $request, AbsenceManagerService $absenceService)
    {
        $request->validate([
            'turnos' => 'required|array',
        ]);

        // Delegamos toda la lógica de los niveles de rescate al servicio de arquitectura
        $absenceService->procesarInasistenciaBarbero($request->input('turnos', []));

        return redirect()->back()->with('success', 'Inasistencias procesadas correctamente bajo el protocolo de rescate.');
    }

    /**
     * Sugiere automáticamente un barbero disponible que tenga hueco libre inmediato para la sucursal, fecha y duración requeridas.
     */
    public function getBarberoSugerido(Request $request)
    {
        $sucursalId = $request->query('sucursal_id');
        $fecha = $request->query('fecha', now()->toDateString());
        $duracionTotal = (int) $request->query('duracion_total', 30);

        if (!$sucursalId) {
            return response()->json(['error' => 'Falta sucursal'], 400);
        }

        // Obtener barberos válidos de la sucursal
        $barberos = Usuario::with('turnoTrabajo')
            ->where('sucursal_id', $sucursalId)
            ->whereIn('rol_id', [1, 2])
            ->where('activo', 1)
            ->get()
            ->filter(fn($b) => isset($b->turnoTrabajo) && !empty($b->turnoTrabajo->hora_inicio));

        $ahora = Carbon::now();
        $esHoy = Carbon::parse($fecha)->isToday();

        foreach ($barberos as $barbero) {
            $hInicio = Carbon::parse($fecha . ' ' . $barbero->turnoTrabajo->hora_inicio);
            $hFin = Carbon::parse($fecha . ' ' . $barbero->turnoTrabajo->hora_fin);

            // Si es hoy y el turno del barbero ya terminó, lo saltamos
            if ($esHoy && $hFin->lte($ahora)) {
                continue;
            }

            // Buscar turnos ocupados de este barbero en la fecha
            $turnosOcupados = Turno::with(['servicio', 'servicios'])
                ->where('barbero_id', $barbero->id)
                ->where('sucursal_id', $sucursalId)
                ->where('fecha', $fecha)
                ->where('estado', 'confirmado')
                ->get();

            $slotActual = $hInicio->copy();

            while ($slotActual->copy()->addMinutes($duracionTotal)->lte($hFin)) {
                $finSlot = $slotActual->copy()->addMinutes($duracionTotal);

                // Validamos que el slot sea al menos 30 min superior a la hora actual si es el mismo día
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

                // Si encontramos un hueco libre, lo retornamos en la respuesta
                if (!$hayConflicto) {
                    return response()->json([
                        'barbero_id' => $barbero->id,
                        'hora' => $slotActual->format('H:i')
                    ]);
                }

                $slotActual->addMinutes(15);
            }
        }

        return response()->json(['barbero_id' => null, 'hora' => null]);
    }
}