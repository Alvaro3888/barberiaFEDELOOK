<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\Servicio;
use App\Models\Pago;
use App\Models\Calificacion;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Método privado auxiliar para agrupar las métricas comunes del admin.
     */
    private function obtenerDatosMetricas()
    {
        $ingresosMes = Turno::whereMonth('fecha', now()->month)
            ->where(function($q) {
                // Web (método mercado_pago): suman siempre que no estén cancelados
                $q->whereHas('pago', function($subQ) {
                    $subQ->where('metodo', 'mercado_pago');
                })->where('estado', '!=', 'cancelado')
                // Desktop (método efectivo o sin mercado_pago): suman solo al completarse
                ->orWhere(function($sub) {
                    $sub->whereHas('pago', function($subQ) {
                        $subQ->where('metodo', '!=', 'mercado_pago');
                    })->where('estado', 'completado');
                });
            })
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

        $turnos = Turno::with(['cliente', 'barbero', 'servicio', 'sucursal', 'pago'])
                       ->orderBy('fecha', 'desc')
                       ->orderBy('hora', 'desc')
                       ->get();

        return compact('ingresosMes', 'totalTurnosMes', 'servicioMasSolicitado', 'barberoDestacado', 'turnos');
    }

    /**
     * Dashboard principal del panel admin.
     */
    public function dashboard(Request $request)
    {
        // 1. Filtros globales
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin    = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        $sucursalId  = $request->input('sucursal_id');
        $barberoId   = $request->input('barbero_id');
        $origen      = $request->input('origen');

        // 2. Query Base con la relación de pagos precargada
        $queryTurnos = Turno::with('pago')->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($sucursalId) {
            $queryTurnos->where('sucursal_id', $sucursalId);
        }
        if ($barberoId) {
            $queryTurnos->where('barbero_id', $barberoId);
        }
        
        // Filtrado por canal basado estrictamente en el método de pago
        if ($origen) {
            if ($origen === 'web') {
                $queryTurnos->whereHas('pago', function($q) {
                    $q->where('metodo', 'mercado_pago');
                });
            } elseif ($origen === 'desktop') {
                $queryTurnos->whereHas('pago', function($q) {
                    $q->where('metodo', '!=', 'mercado_pago');
                });
            }
        }

        // 3. KPIs Principales (Ingresos totales diferenciados por método de pago)
        $totalTurnos     = (clone $queryTurnos)->count();
        
        $ingresosTotales = (clone $queryTurnos)
            ->where(function($q) {
                // Turnos Web (mercado_pago): Suman de inmediato si no están cancelados
                $q->whereHas('pago', function($subQ) {
                    $subQ->where('metodo', 'mercado_pago');
                })->where('estado', '!=', 'cancelado')
                // Turnos Desktop (efectivo): Suman únicamente cuando su estado es completado
                ->orWhere(function($sub) {
                    $sub->whereHas('pago', function($subQ) {
                        $subQ->where('metodo', '!=', 'mercado_pago');
                    })->where('estado', 'completado');
                });
            })
            ->sum('precio_historico');

        $completados = (clone $queryTurnos)->where('estado', 'completado')->count();
        $confirmados = (clone $queryTurnos)->where('estado', 'confirmado')->count();
        $pendientes  = (clone $queryTurnos)->where('estado', 'pendiente')->count();
        $cancelados  = (clone $queryTurnos)->where('estado', 'cancelado')->count();

        // Highlights
        $servicioTop = (clone $queryTurnos)
            ->selectRaw('servicio_id, COUNT(*) as total')
            ->groupBy('servicio_id')
            ->orderByDesc('total')
            ->with('servicio')
            ->first();
        $servicioMasSolicitado = $servicioTop?->servicio?->nombre ?? 'N/A';

        $barberoTop = (clone $queryTurnos)
            ->selectRaw('barbero_id, COUNT(*) as total')
            ->groupBy('barbero_id')
            ->orderByDesc('total')
            ->with('barbero')
            ->first();
        $barberoDestacado = $barberoTop?->barbero?->nombre ?? 'N/A';

        // 4. DATOS PARA LA CURVA EN EJE CARTESIANO (Evolución de Ganancias y Turnos por Día)
        $evoluciónDiaria = (clone $queryTurnos)
            ->get()
            ->groupBy('fecha')
            ->map(function ($turnosDelDia, $fecha) {
                $ingresos = $turnosDelDia->sum(function ($turno) {
                    $esWeb = optional($turno->pago)->metodo === 'mercado_pago';
                    
                    if ($esWeb) {
                        return $turno->estado !== 'cancelado' ? $turno->precio_historico : 0;
                    } else {
                        return $turno->estado === 'completado' ? $turno->precio_historico : 0;
                    }
                });

                return [
                    'fecha' => $fecha,
                    'ingresos' => $ingresos,
                    'cantidad_turnos' => $turnosDelDia->count(),
                ];
            })->values();

        $fechasLabels   = $evoluciónDiaria->pluck('fecha')->map(fn($f) => \Carbon\Carbon::parse($f)->format('d/m'))->toArray();
        $ingresosData   = $evoluciónDiaria->pluck('ingresos')->toArray();
        $turnosEvolData = $evoluciónDiaria->pluck('cantidad_turnos')->toArray();

        // 5. Tabla de últimos movimientos
        $turnos = (clone $queryTurnos)
            ->with(['cliente', 'barbero', 'servicio', 'sucursal', 'pago'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->take(10)
            ->get();

        // Datos para selectores
        $sucursales = \App\Models\Sucursal::all();
        $barberos   = \App\Models\Usuario::whereIn('rol_id', [1, 2])->get();

        return view('admin.dashboard_admin', compact(
            'fechaInicio',
            'fechaFin',
            'sucursalId',
            'barberoId',
            'origen',
            'ingresosTotales',
            'totalTurnos',
            'completados',
            'confirmados',
            'pendientes',
            'cancelados',
            'servicioMasSolicitado',
            'barberoDestacado',
            'fechasLabels',
            'ingresosData',
            'turnosEvolData',
            'turnos',
            'sucursales',
            'barberos'
        ));
    }

    public function estadisticas(Request $request)
    {
        $completados = Turno::where('estado', 'completado')->count();
        $pendientes  = Turno::where('estado', 'pendiente')->count();
        $confirmados = Turno::where('estado', 'confirmado')->count();
        $cancelados  = Turno::where('estado', 'cancelado')->count();

        $serviciosLabels = Servicio::pluck('nombre')->toArray();
        $serviciosData   = Servicio::withCount('turnos')->pluck('turnos_count')->toArray();

        $mesesLabels = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo'];
        $ingresosData = [12000, 15000, 18000, 20000, 17000];

        $metricas = $this->obtenerDatosMetricas();

        if ($request->ajax()) {
            return view('admin.estadisticas.partials.estadisticas_table', array_merge([
                'completados' => $completados,
                'pendientes' => $pendientes,
                'confirmados' => $confirmados,
                'cancelados' => $cancelados,
                'serviciosLabels' => $serviciosLabels,
                'serviciosData' => $serviciosData,
                'mesesLabels' => $mesesLabels,
                'ingresosData' => $ingresosData
            ], $metricas))->render();
        }

        return view('admin.estadisticas.index', array_merge([
            'completados' => $completados,
            'pendientes' => $pendientes,
            'confirmados' => $confirmados,
            'cancelados' => $cancelados,
            'serviciosLabels' => $serviciosLabels,
            'serviciosData' => $serviciosData,
            'mesesLabels' => $mesesLabels,
            'ingresosData' => $ingresosData
        ], $metricas));
    }

    /**
     * Gestión y filtrado de pagos.
     */
    public function pagos(Request $request)
    {
        $query = Pago::with([
            'turno.cliente',
            'turno.servicio',
            'turno.barbero',
            'turno.sucursal'
        ]);

        if ($request->filled('cliente')) {
            $cliente = $request->input('cliente');
            $query->whereHas('turno.cliente', function ($q) use ($cliente) {
                $q->where('nombre', 'like', '%' . $cliente . '%');
            });
        }

        if ($request->filled('servicio_id')) {
            $servicioId = $request->input('servicio_id');
            $query->whereHas('turno', function ($q) use ($servicioId) {
                $q->where('servicio_id', $servicioId);
            });
        }

        if ($request->filled('payment_id')) {
            $paymentId = $request->input('payment_id');
            $query->where('payment_id_mp', 'like', '%' . $paymentId . '%');
        }

        $pagos = $query->orderByDesc('created_at')->get();
        $servicios = Servicio::all();

        return view('admin.pagos.index', compact('pagos', 'servicios'));
    }

    /**
     * Gestión y moderación de calificaciones de clientes (1 a 5 estrellas).
     */
    public function calificaciones(Request $request)
    {
        $query = Calificacion::with(['cliente', 'turno.servicio', 'turno.barbero'])
            ->latest();

        // Filtro por nivel de estrellas (1 a 5)
        if ($request->filled('estrellas')) {
            $query->where('puntuacion', $request->estrellas);
        }

        // Filtro por estado (activos o dados de baja)
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado == 'activos');
        }

        $calificaciones = $query->paginate(15)->withQueryString();

        return view('admin.calificaciones.index', compact('calificaciones'));
    }

    /**
     * Dar de baja o reactivar un comentario (moderación).
     */
    public function toggleCalificacion($id)
    {
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->activo = !$calificacion->activo;
        $calificacion->save();

        $mensaje = $calificacion->activo 
            ? 'El comentario ha sido reactivado y vuelve a ser visible.' 
            : 'El comentario ha sido dado de baja correctamente.';

        return redirect()->back()->with('success', $mensaje);
    }
}