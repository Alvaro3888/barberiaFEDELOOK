@extends('layouts.barbero')

@section('content')
<div class="container-fluid px-0">

    <!-- Encabezado con el Nombre del Barbero -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem;">
                Progreso y Estadísticas de <span style="color: #168F87;">{{ $barbero->nombre ?? $barbero->name }}</span>
            </h2>
            <p class="text-muted small mb-0">Panel de control de rendimiento y métricas de atención en Fede looK.</p>
        </div>

        <!-- Filtros de Rango de Tiempo -->
        <form method="GET" action="{{ route('barbero.progreso') }}" class="d-flex align-items-center gap-2 bg-white p-1 rounded-3 shadow-sm border">
            <select name="rango" id="rango" class="form-select form-select-sm border-0 bg-transparent fw-semibold text-dark" onchange="this.form.submit()">
                <option value="hoy" {{ $rango == 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="semana" {{ $rango == 'semana' ? 'selected' : '' }}>Esta semana</option>
                <option value="mes" {{ $rango == 'mes' ? 'selected' : '' }}>Este mes</option>
                <option value="anio" {{ $rango == 'anio' ? 'selected' : '' }}>Este año</option>
                <option value="todo" {{ $rango == 'todo' ? 'selected' : '' }}>Histórico completo</option>
            </select>
        </form>
    </div>

    <!-- Tarjetas de Métricas Principales -->
    <div class="row g-3 mb-4">
        
        <!-- Total de Turnos -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-secondary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Asignados</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0">{{ $totalTurnos }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-light text-secondary">
                        <i class="bi bi-calendar-range fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Turnos Completados -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4" style="border-color: #168F87 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Completados</span>
                        <h3 class="fw-bold mt-1 mb-0" style="color: #168F87;">{{ $completados }}</h3>
                    </div>
                    <div class="rounded-circle p-3 text-white" style="background-color: #168F87;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendientes / Confirmados -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Pendientes / En curso</span>
                        <h3 class="fw-bold text-warning-emphasis mt-1 mb-0">{{ $pendientes }}</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-warning-subtle text-warning">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Efectividad -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Efectividad</span>
                        <h3 class="fw-bold text-info-emphasis mt-1 mb-0">{{ $efectividad }}%</h3>
                    </div>
                    <div class="rounded-circle p-3 bg-info-subtle text-info">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- SECCIÓN DE GRÁFICOS ESTADÍSTICOS -->
    <div class="row g-3 mb-4">
        
        <!-- Gráfico de Estados (Barras) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill text-petrol me-2"></i>Rendimiento por Estado de Turnos</h5>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="graficoEstados"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Origen (Dona) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill text-petrol me-2"></i>Origen de los Turnos</h5>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="graficoOrigen"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Incluir Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Datos pasados desde el controlador
    const completados = {{ $completados }};
    const pendientes = {{ $pendientes }};
    const cancelados = {{ $cancelados }};

    const online = {{ $online }};
    const mostrador = {{ $mostrador }};

    // 1. Gráfico de Estados (Barras)
    const ctxEstados = document.getElementById('graficoEstados').getContext('2d');
    new Chart(ctxEstados, {
        type: 'bar',
        data: {
            labels: ['Completados', 'Pendientes', 'Cancelados'],
            datasets: [{
                label: 'Cantidad de Turnos',
                data: [completados, pendientes, cancelados],
                backgroundColor: [
                    'rgba(22, 143, 135, 0.85)',
                    'rgba(255, 193, 7, 0.85)',
                    'rgba(220, 53, 69, 0.85)'
                ],
                borderColor: [
                    '#168F87',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });

    // 2. Gráfico de Origen (Dona)
    const ctxOrigen = document.getElementById('graficoOrigen').getContext('2d');
    new Chart(ctxOrigen, {
        type: 'doughnut',
        data: {
            labels: ['Online (Web)', 'Desktop (Mostrador)'],
            datasets: [{
                data: [online, mostrador],
                backgroundColor: [
                    '#168F87',
                    '#3BB5AC'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 12 } }
                }
            }
        }
    });
});
</script>
@endsection