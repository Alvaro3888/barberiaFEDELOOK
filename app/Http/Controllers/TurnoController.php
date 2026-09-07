<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Models\Sucursal;
use Carbon\Carbon;

class TurnoController extends Controller
{
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $usuario = auth()->user();

        if ($usuario->rol->nombre === 'admin') {
            return redirect()->route('admin.dashboard_admin');
        }

        if ($usuario->rol->nombre === 'barbero') {
            return redirect()->route('barbero.dashboard_barbero');
        }

        $servicios  = Servicio::all();
        $sucursales = Sucursal::all();
        $barberos   = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })->get();

        return view('usuario.turnos.create', compact('servicios', 'sucursales', 'barberos'));
    }

    public function store(Request $request)
{
    $request->validate([
        'servicio_id' => 'required|exists:servicios,id',
        'barbero_id'  => 'required|exists:usuarios,id',
        'sucursal_id' => 'required|exists:sucursales,id',
        'fecha'       => 'required|date|after_or_equal:today',
        'hora'        => ['required', function($attribute, $value, $fail) use ($request) {
            $fechaHora = Carbon::parse($request->fecha.' '.$value);

            // No permitir horas pasadas
            if ($fechaHora->isPast()) {
                $fail('La hora seleccionada ya pasó.');
            }

            // Exigir mínimo 30 minutos de anticipación
            if ($fechaHora->diffInMinutes(now(), false) < 30) {
                $fail('El turno debe reservarse con al menos 30 minutos de anticipación.');
            }
        }],
    ]);

    $servicio = Servicio::findOrFail($request->servicio_id);

    Turno::create([
        'cliente_id'       => auth()->id(),
        'barbero_id'       => $request->barbero_id,
        'servicio_id'      => $request->servicio_id,
        'sucursal_id'      => $request->sucursal_id,
        'fecha'            => $request->fecha,
        'hora'             => $request->hora,
        'estado'           => 'confirmado',   // arranca confirmado
        'precio_historico' => $servicio->precio_actual,
    ]);

    return redirect()->route('usuario.turnos')->with('success', 'Turno reservado correctamente.');
}


    public function edit($id)
    {
        $turno = Turno::findOrFail($id);

        $horaTurno = Carbon::parse($turno->fecha.' '.$turno->hora);
        $minutosRestantes = now()->diffInMinutes($horaTurno, false);

        if (auth()->user()->rol->nombre !== 'cliente' || $turno->estado !== 'confirmado' || $minutosRestantes < 30) {
            return redirect()->route('usuario.turnos')->with('error', 'No podés editar este turno.');
        }

        $servicios  = Servicio::all();
        $sucursales = Sucursal::all();
        $barberos   = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })->get();

        return view('usuario.turnos.edit', compact('turno', 'servicios', 'sucursales', 'barberos'));
    }

    public function update(Request $request, $id)
{
    $turno = Turno::findOrFail($id);

    if (auth()->user()->rol->nombre === 'cliente') {
        $request->validate([
            'servicio_id' => 'required|exists:servicios,id',
            'barbero_id'  => 'required|exists:usuarios,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha'       => 'required|date|after_or_equal:today',
            'hora'        => ['required', function($attribute, $value, $fail) use ($request, $turno) {
                $fechaHora = Carbon::parse($request->fecha.' '.$value);

                // No permitir horas pasadas
                if ($fechaHora->isPast()) {
                    $fail('La hora seleccionada ya pasó.');
                }

                // Exigir mínimo 30 minutos de anticipación
                if ($fechaHora->diffInMinutes(now(), false) < 30) {
                    $fail('El turno debe reservarse con al menos 30 minutos de anticipación.');
                }
            }],
        ]);

        // Validación de solapamiento de barbero (45 min)
        $horaInicio = Carbon::parse($request->fecha.' '.$request->hora);
        $horaFin    = $horaInicio->copy()->addMinutes(45);

        $conflicto = Turno::where('barbero_id', $request->barbero_id)
            ->where('fecha', $request->fecha)
            ->whereBetween('hora', [$horaInicio->format('H:i'), $horaFin->format('H:i')])
            ->where('id', '!=', $turno->id)
            ->exists();

        if ($conflicto) {
            return redirect()->back()->withErrors(['hora' => 'Ese barbero ya tiene un turno en ese rango de horario.']);
        }

        $turno->update([
            'servicio_id' => $request->servicio_id,
            'barbero_id'  => $request->barbero_id,
            'sucursal_id' => $request->sucursal_id,
            'fecha'       => $request->fecha,
            'hora'        => $request->hora,
        ]);

        return redirect()->route('usuario.turnos')->with('success', 'Turno actualizado correctamente.');
    }

    $request->validate([
        'estado' => 'required|in:confirmado,completado,cancelado',
    ]);

    $turno->estado = $request->estado;
    $turno->save();

    return redirect()->back()->with('success', 'El turno fue actualizado correctamente.');
}


    public function destroy($id)
    {
        $turno = Turno::findOrFail($id);

        if ($turno->cliente_id !== auth()->id()) {
            abort(403, 'No tenés permiso para cancelar este turno.');
        }

        $horaTurno = Carbon::parse($turno->fecha.' '.$turno->hora);
        $minutosRestantes = now()->diffInMinutes($horaTurno, false);

        if ($minutosRestantes < 30) {
            return redirect()->route('usuario.turnos')->with('error', 'No se puede cancelar el turno faltando menos de 30 minutos.');
        }

        if ($turno->estado === 'confirmado') {
            $turno->estado = 'cancelado';
            $turno->save();
        }

        return redirect()->route('usuario.turnos')->with('success', 'Turno cancelado correctamente.');
    }

    public function misTurnosBarbero()
    {
        $turnos = Turno::with(['cliente','servicio','sucursal'])
                       ->where('barbero_id', auth()->id())
                       ->orderBy('fecha')
                       ->orderBy('hora')
                       ->get();

        return view('barbero.dashboard_barbero', compact('turnos'));
    }

    public function misTurnosUsuario()
    {
        $turnos = Turno::with(['barbero','servicio','sucursal'])
                       ->where('cliente_id', auth()->id())
                       ->orderBy('fecha')
                       ->orderBy('hora')
                       ->get();

        return view('usuario.turnos.index', compact('turnos'));
    }

    public function index(Request $request)
    {
        $query = Turno::with(['cliente','barbero','servicio','sucursal'])
                      ->orderBy('fecha')
                      ->orderBy('hora');

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->sucursal_id);
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

        $turnos     = $query->get();
        $sucursales = Sucursal::all();

        if ($request->ajax()) {
            return view('admin.turnos.partials.turnos_table', compact('turnos'))->render();
        }

        return view('admin.turnos.index', compact('turnos','sucursales'));
    }

    public function getBarberosPorSucursal($sucursalId)
    {
        $barberos = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'barbero');
        })
        ->where('sucursal_id', $sucursalId)
        ->get(['id','nombre']);   // 👈 coincide con tu base

        return response()->json($barberos);
    }
}
