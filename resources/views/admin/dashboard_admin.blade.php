@extends('layouts.app')

@section('content')
<style>
    /* Estilos avanzados con la paleta Fede Look */
    :root {
        --petrol: #0d9488;
        --petrol-dark: #0f746e;
        --petrol-soft: #f0fdfa;
        --border-soft: #e2e8f0;
    }

    .dashboard-fixed-wrapper {
        width: 100%;
        max-width: 100%;
    }

    .text-petrol {
        color: var(--petrol) !important;
    }

    .bg-petrol-soft {
        background-color: var(--petrol-soft) !important;
    }

    .border-petrol {
        border-color: var(--petrol) !important;
    }

    /* Header del Dashboard Moderno */
    .dashboard-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(13, 148, 136, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .admin-badge-glow {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
    }

    .admin-badge-glow:hover {
        background: rgba(13, 148, 136, 0.2);
        border-color: var(--petrol);
        transform: translateY(-2px);
    }

    /* Tarjetas de Secciones con Divisor Visual */
    .section-divider {
        position: relative;
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(13, 148, 136, 0.3), transparent);
        margin: 1.5rem 0;
    }

    .filter-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .extra-small-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: block;
        letter-spacing: 0.06em;
    }

    /* Métricas con animación suave */
    .card-metric {
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid var(--border-soft);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
        overflow: hidden;
    }

    .card-metric::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: transparent;
        transition: background 0.3s ease;
    }

    .card-metric:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(13, 148, 136, 0.08) !important;
        border-color: #ccfbf1;
    }

    .card-metric:hover::after {
        background: var(--petrol);
    }

    /* Contenedores de Gráficos y Tablas */
    .dashboard-card-container {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-soft);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        transition: box-shadow 0.3s ease;
    }

    .dashboard-card-container:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.04);
    }

    .table-modern thead th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: 0.03em;
    }
</style>

<div class="dashboard-fixed-wrapper pb-4">
    
    <!-- ENCABEZADO MODERNO CON GLOW -->
    <div class="dashboard-header p-4 mb-4 shadow-sm">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative z-1">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <i class="bi bi-speedometer2 fs-4"></i>
                </div>
                <div>
                    <h1 class="h4 text-white fw-bold mb-1">Centro de Control</h1>
                    <p class="text-white-50 small mb-0">Evolución financiera y rendimiento general por canal en tiempo real</p>
                </div>
            </div>
            <div>
                <div class="admin-badge-glow px-3 py-2 rounded-pill d-flex align-items-center gap-2 text-white shadow-xs">
                    <span class="p-1 bg-success rounded-circle animate-pulse" style="width: 8px; height: 8px; display: inline-block;"></span>
                    <span class="small fw-semibold">Admin:</span>
                    <span class="badge bg-petrol-soft text-petrol fw-bold px-2.5 py-1 rounded-pill">{{ Auth::user()->nombre ?? Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. BARRA DE FILTROS EN VIVO -->
    <div class="card filter-card mb-4">
        <div class="card-body p-3.5">
            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                <i class="bi bi-funnel-fill text-petrol fs-6"></i>
                <span class="text-uppercase fw-bold text-dark small" style="font-size: 0.75rem; letter-spacing: 0.05em;">Filtros de Análisis</span>
            </div>
            <form method="GET" action="{{ route('admin.dashboard_admin') }}" id="liveFilterForm" class="row g-3 align-items-end">
                
                <div class="col-6 col-md-3 col-xl-2">
                    <label class="extra-small-label"><i class="bi bi-calendar-event me-1"></i> Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm bg-light border-0 shadow-none py-2" onchange="this.form.submit()">
                </div>

                <div class="col-6 col-md-3 col-xl-2">
                    <label class="extra-small-label"><i class="bi bi-calendar-check me-1"></i> Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm bg-light border-0 shadow-none py-2" onchange="this.form.submit()">
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <label class="extra-small-label"><i class="bi bi-geo-alt me-1"></i> Sucursal</label>
                    <select name="sucursal_id" class="form-select form-select-sm bg-light border-0 shadow-none py-2" onchange="this.form.submit()">
                        <option value="">Todas las sucursales</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}" {{ $sucursalId == $suc->id ? 'selected' : '' }}>
                                {{ $suc->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-xl-2">
                    <label class="extra-small-label"><i class="bi bi-person-badge me-1"></i> Barbero</label>
                    <select name="barbero_id" class="form-select form-select-sm bg-light border-0 shadow-none py-2" onchange="this.form.submit()">
                        <option value="">Todos los barberos</option>
                        @foreach($barberos as $barb)
                            <option value="{{ $barb->id }}" {{ $barberoId == $barb->id ? 'selected' : '' }}>
                                {{ $barb->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-4 col-xl-2">
                    <label class="extra-small-label"><i class="bi bi-hdd-network me-1"></i> Origen / Canal</label>
                    <select name="origen" class="form-select form-select-sm bg-light border-0 shadow-none py-2" onchange="this.form.submit()">
                        <option value="">Web y Desktop</option>
                        <option value="web" {{ $origen == 'web' ? 'selected' : '' }}>Web (Clientes)</option>
                        <option value="desktop" {{ $origen == 'desktop' ? 'selected' : '' }}>Desktop / Mostrador</option>
                    </select>
                </div>

                <!-- Botón de Limpieza -->
                <div class="col-12 col-md-4 col-xl-2 d-grid">
                    <label class="extra-small-label d-none d-xl-block">&nbsp;</label>
                    <a href="{{ route('admin.dashboard_admin') }}" class="btn btn-light text-secondary btn-sm fw-bold d-flex align-items-center justify-content-center gap-1 border py-2" title="Restablecer Filtros">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Limpiar Filtros</span>
                    </a>
                </div>

            </form>
        </div>
    </div>

    <!-- SEPARADOR VISUAL DE SECCIÓN -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Indicadores Clave de Rendimiento (KPIs)</span>
        <div class="flex-grow-1 border-bottom border-light"></div>
    </div>

    <!-- 2. TARJETAS DE MÉTRICAS CLAVE -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-metric shadow-xs h-100 border-start border-4 border-petrol">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold d-mb-1" style="font-size: 0.68rem;">Ingresos Totales</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">AR$ {{ number_format($ingresosTotales, 0, ',', '.') }}</h4>
                        </div>
                        <div class="text-petrol bg-petrol-soft p-3 rounded-4 shadow-2xs">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-metric shadow-xs h-100 border-start border-success border-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold d-mb-1" style="font-size: 0.68rem;">Volumen de Turnos</span>
                            <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalTurnos }}</h4>
                        </div>
                        <div class="text-success bg-success-subtle p-3 rounded-4 shadow-2xs">
                            <i class="bi bi-calendar2-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-metric shadow-xs h-100 border-start border-warning border-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="max-width: 72%;">
                            <span class="text-muted small text-uppercase fw-bold d-mb-1" style="font-size: 0.68rem;">Servicio Top</span>
                            <h5 class="fw-bold text-dark mb-0 mt-1 text-truncate fs-6" title="{{ $servicioMasSolicitado }}">{{ $servicioMasSolicitado }}</h5>
                        </div>
                        <div class="text-warning bg-warning-subtle p-3 rounded-4 shadow-2xs">
                            <i class="bi bi-scissors fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-metric shadow-xs h-100 border-start border-info border-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="max-width: 72%;">
                            <span class="text-muted small text-uppercase fw-bold d-mb-1" style="font-size: 0.68rem;">Barbero Destacado</span>
                            <h5 class="fw-bold text-dark mb-0 mt-1 text-truncate fs-6" title="{{ $barberoDestacado }}">{{ $barberoDestacado }}</h5>
                        </div>
                        <div class="text-info bg-info-subtle p-3 rounded-4 shadow-2xs">
                            <i class="bi bi-award fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEPARADOR VISUAL DE SECCIÓN -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Analítica Gráfica y Distribución</span>
        <div class="flex-grow-1 border-bottom border-light"></div>
    </div>

    <!-- 3. GRÁFICOS INTERACTIVOS -->
    <div class="row g-3 mb-4">
        <!-- Gráfico de Evolución -->
        <div class="col-12 col-xl-8">
            <div class="dashboard-card-container h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="card-title text-dark fw-bold mb-0">Evolución Diaria</h6>
                        <p class="text-muted small mb-0">Comparativa de ganancias monetarias frente a volumen de turnos.</p>
                    </div>
                    <span class="badge bg-light text-petrol border border-petrol-subtle px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size: 0.7rem;">Cartesiano en Vivo</span>
                </div>
                <div style="position: relative; height:270px; width: 100%;">
                    <canvas id="graficoEvolucionCurva"></canvas>
                </div>
            </div>
        </div>

        <!-- Estado de la Agenda -->
        <div class="col-12 col-xl-4">
            <div class="dashboard-card-container h-100 p-4">
                <h6 class="card-title text-dark fw-bold mb-1">Estado de la Agenda</h6>
                <p class="text-muted small mb-3">Distribución proporcional de estados.</p>
                <div style="position: relative; height:160px; width: 100%;">
                    <canvas id="graficoTortaEstados"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between small text-muted mb-1.5" style="font-size: 0.8rem;">
                        <span><i class="bi bi-circle-fill text-success me-1"></i> Completados:</span>
                        <strong class="text-dark">{{ $completados }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1.5" style="font-size: 0.8rem;">
                        <span><i class="bi bi-circle-fill text-petrol me-1"></i> Confirmados:</span>
                        <strong class="text-dark">{{ $confirmados }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1.5" style="font-size: 0.8rem;">
                        <span><i class="bi bi-circle-fill text-warning me-1"></i> Pendientes:</span>
                        <strong class="text-dark">{{ $pendientes }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted" style="font-size: 0.8rem;">
                        <span><i class="bi bi-circle-fill text-danger me-1"></i> Cancelados:</span>
                        <strong class="text-dark">{{ $cancelados }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SEPARADOR VISUAL DE SECCIÓN -->
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.1em;">Registro de Actividad Reciente</span>
        <div class="flex-grow-1 border-bottom border-light"></div>
    </div>

    <!-- 4. TABLA DE ÚLTIMOS MOVIMIENTOS -->
    <div class="dashboard-card-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="card-title text-dark fw-bold mb-0">Últimos Movimientos del Período</h6>
                <p class="text-muted small mb-0">Listado interactivo de transacciones y reservas registradas.</p>
            </div>
            <a href="{{ route('admin.turnos.index') }}" class="btn btn-sm btn-light text-petrol border border-petrol-subtle fw-bold rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 shadow-2xs">
                <span>Gestión de Turnos</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-modern" style="font-size: 0.85rem;">
                <thead>
                    <tr>
                        <th class="py-3 ps-3 rounded-start-3">Fecha / Hora</th>
                        <th class="py-3">Cliente</th>
                        <th class="py-3">Sucursal</th>
                        <th class="py-3">Barbero</th>
                        <th class="py-3">Servicio</th>
                        <th class="py-3">Canal</th>
                        <th class="py-3 text-center rounded-end-3">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($turnos as $turno)
                        <tr>
                            <td class="fw-semibold text-dark ps-3">
                                {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
                                <small class="text-muted d-block fw-normal">{{ substr($turno->hora, 0, 5) }} hs</small>
                            </td>
                            <td>{{ $turno->cliente_nombre_manual ?? $turno->cliente->nombre ?? 'Cliente Ocasional' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $turno->sucursal->nombre ?? 'N/A' }}</span></td>
                            <td>{{ $turno->barbero->nombre ?? 'N/A' }}</td>
                            <td class="fw-medium">{{ $turno->servicio->nombre ?? 'N/A' }}</td>
                            <td>
                                @if(!empty($turno->cliente_nombre_manual) || ($turno->cliente && $turno->cliente->email === 'mostrador@barberia.com'))
                                    <span class="badge bg-secondary-subtle text-dark border px-2.5 py-1 rounded-pill">Desktop</span>
                                @else
                                    <span class="badge bg-petrol-soft text-petrol border border-petrol-subtle px-2.5 py-1 rounded-pill">Web</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                @if($turno->estado == 'completado')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold">Completado</span>
                                @elseif($turno->estado == 'confirmado')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill fw-semibold">Confirmado</span>
                                @elseif($turno->estado == 'pendiente')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold">Pendiente</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-semibold">Cancelado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <div class="py-3">
                                    <i class="bi bi-inbox fs-2 text-muted opacity-50 mb-2 d-block"></i>
                                    <span>No hay registros para los filtros seleccionados.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CDN Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctxCurva = document.getElementById('graficoEvolucionCurva').getContext('2d');
    
    let gradient = ctxCurva.createLinearGradient(0, 0, 0, 270);
    gradient.addColorStop(0, 'rgba(13, 148, 136, 0.3)');
    gradient.addColorStop(1, 'rgba(13, 148, 136, 0.0)');

    new Chart(ctxCurva, {
        type: 'line',
        data: {
            labels: @json($fechasLabels),
            datasets: [
                {
                    label: 'Ganancias (AR$)',
                    data: @json($ingresosData),
                    borderColor: '#0d9488',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#0f746e',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Cantidad de Turnos',
                    data: @json($turnosEvolData),
                    borderColor: '#1e293b',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    tension: 0.3,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1e293b',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                tooltip: {
                    padding: 12,
                    backgroundColor: '#0f172a',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) { label += ': '; }
                            if (context.datasetIndex === 0) {
                                label += 'AR$ ' + new Intl.NumberFormat('es-AR').format(context.raw);
                            } else {
                                label += context.raw + ' turno(s)';
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    labels: { boxWidth: 14, font: { size: 11, weight: '500' }, color: '#475569' }
                }
            },
            scales: {
                x: { 
                    grid: { display: false },
                    ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10, font: { size: 10 }, color: '#64748b' }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Ingresos ($)', font: { size: 10, weight: 'bold' }, color: '#64748b' },
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        font: { size: 10 },
                        color: '#64748b',
                        callback: function(value) {
                            return '$' + new Intl.NumberFormat('es-AR').format(value);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Turnos', font: { size: 10, weight: 'bold' }, color: '#64748b' },
                    grid: { drawOnChartArea: false },
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        font: { size: 10 },
                        color: '#64748b'
                    }
                }
            }
        }
    });

    const ctxTorta = document.getElementById('graficoTortaEstados').getContext('2d');
    new Chart(ctxTorta, {
        type: 'doughnut',
        data: {
            labels: ['Completados', 'Confirmados', 'Pendientes', 'Cancelados'],
            datasets: [{
                data: [{{ $completados }}, {{ $confirmados }}, {{ $pendientes }}, {{ $cancelados }}],
                backgroundColor: ['#198754', '#0d9488', '#ffc107', '#dc3545'],
                hoverOffset: 6,
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '75%'
        }
    });

});
</script>
@endsection