<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Servicio;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard principal del panel admin.
     */
    public function dashboard()
    {
        $ingresosMes = Turno::whereMonth('fecha', now()->month)
                            ->where('estado','confirmado')
                            ->sum('precio_historico');

        $totalTurnosMes = Turno::whereMonth('fecha', now()->month)->count();

        $servicioMasSolicitado = Servicio::withCount('turnos')
                                         ->orderByDesc('turnos_count')
                                         ->first()?->nombre;

        $barberoDestacado = Turno::with('barbero')
                                 ->selectRaw('barbero_id, COUNT(*) as total')
                                 ->groupBy('barbero_id')
                                 ->orderByDesc('total')
                                 ->first()?->barbero?->nombre;

        // Lista de turnos para la tabla del dashboard
        $turnos = Turno::with(['cliente','barbero','servicio','sucursal'])
                       ->orderBy('fecha','desc')
                       ->orderBy('hora','desc')
                       ->get();

        // ✅ Vista correcta: resources/views/admin/dashboard_admin.blade.php
        return view('admin.dashboard_admin', compact(
            'ingresosMes',
            'totalTurnosMes',
            'servicioMasSolicitado',
            'barberoDestacado',
            'turnos'
        ));
    }

    /**
     * Vista de estadísticas detalladas (panel admin).
     */
    public function estadisticas(Request $request)
    {
        $pendientes  = Turno::where('estado','pendiente')->count();
        $confirmados = Turno::where('estado','confirmado')->count();
        $cancelados  = Turno::where('estado','cancelado')->count();

        $serviciosLabels = Servicio::pluck('nombre')->toArray();
        $serviciosData   = Servicio::withCount('turnos')->pluck('turnos_count')->toArray();

        $mesesLabels = ['Enero','Febrero','Marzo','Abril','Mayo'];
        $ingresosData = [12000, 15000, 18000, 20000, 17000];

        $ingresosMes = Turno::whereMonth('fecha', now()->month)
                            ->where('estado','confirmado')
                            ->sum('precio_historico');

        $totalTurnosMes = Turno::whereMonth('fecha', now()->month)->count();

        $servicioMasSolicitado = Servicio::withCount('turnos')
                                         ->orderByDesc('turnos_count')
                                         ->first()?->nombre;

        $barberoDestacado = Turno::with('barbero')
                                 ->selectRaw('barbero_id, COUNT(*) as total')
                                 ->groupBy('barbero_id')
                                 ->orderByDesc('total')
                                 ->first()?->barbero?->nombre;
        // Lista de turnos para la tabla del dashboard
        $turnos = Turno::with(['cliente','barbero','servicio','sucursal'])
                       ->orderBy('fecha','desc')
                       ->orderBy('hora','desc')
                       ->get();


        if ($request->ajax()) {
            return view('admin.estadisticas.partials.estadisticas_table', compact(
                'pendientes',
                'confirmados',
                'cancelados',
                'serviciosLabels',
                'serviciosData',
                'mesesLabels',
                'ingresosData'
            ))->render();
        }

        // ✅ Vista correcta: resources/views/admin/estadisticas/index.blade.php
        return view('admin.estadisticas.index', compact(
            'pendientes',
            'confirmados',
            'cancelados',
            'serviciosLabels',
            'serviciosData',
            'mesesLabels',
            'ingresosData',
            'ingresosMes',
                        'totalTurnosMes',
            'servicioMasSolicitado',
            'barberoDestacado',
            'turnos'

        ));
    }
}
