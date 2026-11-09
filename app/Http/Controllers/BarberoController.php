<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Turno;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BarberoController extends Controller
{
    /**
     * Muestra el panel operativo del barbero autenticado con sus turnos filtrados y segmentados 
     * en tres bloques temporales inteligentes: Turnos de Hoy, Futuros y Finalizados/Vencidos.
     * Soporta peticiones AJAX para actualización dinámica de tablas.
     */
    public function misTurnosBarbero(Request $request)
    {
        $barberoId = auth()->id();
        $hoy = Carbon::today()->toDateString();
        $ahora = Carbon::now();

        // Aseguramos cargar la relación 'servicio' para leer su duración exacta en minutos
        $query = Turno::with(['cliente', 'servicio', 'sucursal'])
            ->where('barbero_id', $barberoId);

        // Filtro opcional por nombre o email del cliente
        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%')
                  ->orWhere('email', 'like', '%' . $request->cliente . '%');
            });
        }

        // Filtro opcional por fecha específica
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        // Filtro opcional por estado del turno
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $todosLosTurnos = $query->get();

        // 1. Turnos de HOY (Permanecen aquí activos hasta que termine formalmente la hora de fin del servicio)
        $turnosHoy = $todosLosTurnos->filter(function ($t) use ($hoy, $ahora) {
            if ($t->fecha !== $hoy || in_array($t->estado, ['completado', 'cancelado'])) {
                return false;
            }

            $inicioTurno = Carbon::parse($t->fecha . ' ' . $t->hora);
            $duracionMinutos = $t->servicio->duracion ?? 30;
            $finTurno = (clone $inicioTurno)->addMinutes($duracionMinutos);

            // El turno sigue en "Hoy" siempre y cuando la hora actual sea menor o igual al horario de finalización
            return $ahora->lte($finTurno);
        })->sortBy(function ($t) {
            return $t->fecha . ' ' . $t->hora;
        });

        // 2. Turnos FUTUROS (Correspondientes a días posteriores al día de hoy)
        $turnosFuturos = $todosLosTurnos->filter(function ($t) use ($hoy) {
            return $t->fecha > $hoy 
                && !in_array($t->estado, ['completado', 'cancelado']);
        })->sortBy(function ($t) {
            return $t->fecha . ' ' . $t->hora;
        });

        // 3. Turnos TERMINADOS / VENCIDOS (Aquellos cuya hora de fin ya transcurrió hoy, fechas pasadas, o estados completados/cancelados)
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

        // Retorno condicional si la petición es asíncrona (AJAX) para refrescar parciales de vistas
        if ($request->ajax()) {
            return view('barbero.partials.tabla_turnos', compact('turnosHoy', 'turnosFuturos', 'turnosFinalizados'))->render();
        }

        return view('barbero.dashboard_barbero', compact('turnosHoy', 'turnosFuturos', 'turnosFinalizados'));
    }

    /**
     * Calcula las estadísticas de rendimiento y progreso del barbero autenticado 
     * según rangos temporales (hoy, semana, mes, año), desglosando volumen de turnos, 
     * canales online vs mostrador y tasa de efectividad de atención.
     */
    public function progresoBarbero(Request $request)
    {
        $barbero = auth()->user(); 
        $barberoId = $barbero->id;

        $rango = $request->input('rango', 'mes'); // Por defecto evalúa el mes actual

        $query = Turno::where('barbero_id', $barberoId);

        // Aplicación del filtro temporal según el rango solicitado
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

        // Diferenciación de canales: Online (Mercado Pago) vs Mostrador/Presencial
        $online = (clone $query)->get()->filter(fn($t) => method_exists($t, 'esPagoOnline') && $t->esPagoOnline())->count();
        $mostrador = $totalTurnos - $online;

        // Cálculo de la efectividad de atención porcentual (turnos completados sobre el total)
        $efectividad = $totalTurnos > 0 ? round(($completados / $totalTurnos) * 100, 1) : 0;

        return view('barbero.progreso', compact(
            'barbero', 'totalTurnos', 'completados', 'pendientes', 'cancelados', 
            'online', 'mostrador', 'efectividad', 'rango'
        ));
    }
}