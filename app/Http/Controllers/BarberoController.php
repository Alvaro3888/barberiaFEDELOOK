<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BarberoController extends Controller
{
    public function misTurnosBarbero(Request $request)
    {
        $barberoId = auth()->id();
        $hoy = Carbon::today()->toDateString();
        $ahora = Carbon::now();

        // Aseguramos cargar la relación 'servicio' para leer su duración
        $query = Turno::with(['cliente', 'servicio', 'sucursal'])
            ->where('barbero_id', $barberoId);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%')
                  ->orWhere('email', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $todosLosTurnos = $query->get();

        // Turnos de HOY (Permanecen aquí hasta que termine la hora de fin del servicio)
        $turnosHoy = $todosLosTurnos->filter(function ($t) use ($hoy, $ahora) {
            if ($t->fecha !== $hoy || in_array($t->estado, ['completado', 'cancelado'])) {
                return false;
            }

            $inicioTurno = Carbon::parse($t->fecha . ' ' . $t->hora);
            $duracionMinutos = $t->servicio->duracion ?? 30;
            $finTurno = (clone $inicioTurno)->addMinutes($duracionMinutos);

            // El turno sigue en "Hoy" siempre y cuando aún no haya terminado su horario de fin
            return $ahora->lte($finTurno);
        })->sortBy(function ($t) {
            return $t->fecha . ' ' . $t->hora;
        });

        // Turnos FUTUROS (Días posteriores)
        $turnosFuturos = $todosLosTurnos->filter(function ($t) use ($hoy) {
            return $t->fecha > $hoy 
                && !in_array($t->estado, ['completado', 'cancelado']);
        })->sortBy(function ($t) {
            return $t->fecha . ' ' . $t->hora;
        });

        // Turnos TERMINADOS / VENCIDOS (Ya pasó su horario de fin o fueron completados/cancelados)
        $turnosFinalizados = $todosLosTurnos->filter(function ($t) use ($hoy, $ahora) {
            $inicioTurno = Carbon::parse($t->fecha . ' ' . $t->hora);
            $duracionMinutos = $t->servicio->duracion ?? 30;
            $finTurno = (clone $inicioTurno)->addMinutes($duracionMinutos);
            
            $esCompletadoOCancelado = in_array($t->estado, ['completado', 'cancelado']);
            $esFechaPasada = $t->fecha < $hoy;
            $esFinPasadoHoy = ($t->fecha === $hoy && $ahora->gt($finTurno));

            return $esCompletadoOCancelado || $esFechaPasada || $esFinPasadoHoy;
        })->sortByDesc(function ($t) {
            return $t->fecha . ' ' . $t->hora;
        });

        if ($request->ajax()) {
            return view('barbero.partials.tabla_turnos', compact('turnosHoy', 'turnosFuturos', 'turnosFinalizados'))->render();
        }

        return view('barbero.dashboard_barbero', compact('turnosHoy', 'turnosFuturos', 'turnosFinalizados'));
    }

    public function progresoBarbero(Request $request)
    {
        $barbero = auth()->user(); 
        $barberoId = $barbero->id;

        $rango = $request->input('rango', 'mes'); 

        $query = Turno::where('barbero_id', $barberoId);

        if ($rango === 'hoy') {
            $query->whereDate('fecha', now()->today());
        } elseif ($rango === 'semana') {
            $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($rango === 'mes') {
            $query->whereMonth('fecha', now()->month)
                  ->whereYear('fecha', now()->year);
        } elseif ($rango === 'anio') {
            $query->whereYear('fecha', now()->year);
        }

        $totalTurnos = (clone $query)->count();
        $completados = (clone $query)->where('estado', 'completado')->count();
        $pendientes  = (clone $query)->whereIn('estado', ['pendiente', 'confirmado'])->count();
        $cancelados  = (clone $query)->where('estado', 'cancelado')->count();

        $online = (clone $query)->get()->filter(fn($t) => method_exists($t, 'esPagoOnline') && $t->esPagoOnline())->count();
        $mostrador = $totalTurnos - $online;

        $efectividad = $totalTurnos > 0 ? round(($completados / $totalTurnos) * 100, 1) : 0;

        return view('barbero.progreso', compact(
            'barbero', 'totalTurnos', 'completados', 'pendientes', 'cancelados', 
            'online', 'mostrador', 'efectividad', 'rango'
        ));
    }
}