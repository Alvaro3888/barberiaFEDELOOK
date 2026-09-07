@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="text-primary">Panel de Administrador</h1>
        <span class="badge bg-secondary fs-6">
            Bienvenido, {{ Auth::user()->nombre }}
        </span>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <!-- Tarjetas informativas (estadísticas generales) -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <i class="bi bi-cash-stack fs-2 text-dark"></i>
                    <h6 class="mt-2">Ingresos</h6>
                    <p class="fw-bold">AR$ {{ $ingresosMes ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-event fs-2 text-dark"></i>
                    <h6 class="mt-2">Turnos este mes</h6>
                    <p class="fw-bold">{{ $totalTurnosMes ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <i class="bi bi-scissors fs-2 text-dark"></i>
                    <h6 class="mt-2">Servicio más solicitado</h6>
                    <p class="fw-bold">{{ $servicioMasSolicitado ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body text-center">
                    <i class="bi bi-star fs-2 text-dark"></i>
                    <h6 class="mt-2">Barbero destacado</h6>
                    <p class="fw-bold">{{ $barberoDestacado ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de navegación -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-2 text-primary"></i>
                    <h5 class="mt-2">Usuarios</h5>
                    <p class="text-muted">Gestión de clientes y barberos</p>
                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-primary">Ver usuarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-2 text-success"></i>
                    <h5 class="mt-2">Turnos</h5>
                    <p class="text-muted">Agenda completa de turnos</p>
                    <a href="{{ route('admin.turnos.index') }}" class="btn btn-outline-success">Ver turnos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart fs-2 text-warning"></i>
                    <h5 class="mt-2">Estadísticas</h5>
                    <p class="text-muted">Reportes y análisis</p>
                    <a href="{{ route('admin.estadisticas') }}" class="btn btn-outline-warning">Ver estadísticas</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
