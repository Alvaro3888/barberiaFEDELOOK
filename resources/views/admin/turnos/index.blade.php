@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4 admin-turnos-dashboard">

    <!-- Alertas de Sesión -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-4 mb-4 border-0 bg-success text-white" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4 border-0 bg-danger text-white" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Encabezado Principal del Administrador -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-12 col-md-8">
            <span class="badge bg-dark text-white px-3 py-1 rounded-pill small fw-semibold mb-2 shadow-sm" style="background-color: #111827 !important;">
                <i class="bi bi-shield-fill-check text-warning me-1"></i> Panel de Control Global - Administrador
            </span>
            <h2 class="fw-bold text-dark mb-0" style="font-family: 'Bebas Neue', sans-serif; font-size: 2.8rem; letter-spacing: 1px;">
                Gestión General de <span style="color: #168F87;">Turnos</span>
            </h2>
            <p class="text-muted small mb-0 mt-1">Supervisión completa de citas, control de origen y ejecución contable.</p>
        </div>
        <div class="col-12 col-md-4 text-md-end">
            <button type="button" class="btn btn-dark fw-bold rounded-pill shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#modalNuevoTurnoAdmin" style="background-color: #111827;">
                <i class="bi bi-plus-circle me-1"></i> Nuevo Turno (Manual)
            </button>
        </div>
    </div>

    <!-- Filtros en Vivo con Tijeras -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 p-4 bg-white">
        <form method="GET" action="{{ route('admin.turnos.index') }}" class="row g-3 align-items-end" id="formFiltros">
            <div class="col-12 col-sm-6 col-md-2">
                <label for="tipo_turno" class="form-label text-muted fw-bold small text-uppercase">Origen</label>
                <select name="tipo_turno" id="tipo_turno" class="form-select form-select-sm shadow-sm rounded-pill border py-2 px-3">
                    <option value="">Todos</option>
                    <option value="online" {{ request('tipo_turno') == 'online' ? 'selected' : '' }}>Pagos Online</option>
                    <option value="desktop" {{ request('tipo_turno') == 'desktop' ? 'selected' : '' }}>Desktop / Efectivo</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="sucursal_id" class="form-label text-muted fw-bold small text-uppercase">Sucursal</label>
                <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm shadow-sm rounded-pill border py-2 px-3">
                    <option value="">Todas</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}" {{ request('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                            {{ $sucursal->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="barbero_id" class="form-label text-muted fw-bold small text-uppercase">Barbero</label>
                <select name="barbero_id" id="barbero_id" class="form-select form-select-sm shadow-sm rounded-pill border py-2 px-3">
                    <option value="">Todos</option>
                    @if(isset($barberos))
                        @foreach ($barberos as $barbero)
                            <option value="{{ $barbero->id }}" 
                                    data-sucursal="{{ $barbero->sucursal_id }}" 
                                    {{ request('barbero_id') == $barbero->id ? 'selected' : '' }}>
                                {{ $barbero->nombre ?? $barbero->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="range" class="form-label text-muted fw-bold small text-uppercase">Período</label>
                <select name="range" id="range" class="form-select form-select-sm shadow-sm rounded-pill border py-2 px-3">
                    <option value="">Todos</option>
                    <option value="hoy" {{ request('range') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                    <option value="3" {{ request('range') == '3' ? 'selected' : '' }}>Últimos 3 días</option>
                    <option value="7" {{ request('range') == '7' ? 'selected' : '' }}>Últimos 7 días</option>
                    <option value="30" {{ request('range') == '30' ? 'selected' : '' }}>Últimos 30 días</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="estado" class="form-label text-muted fw-bold small text-uppercase">Estado</label>
                <select name="estado" id="estado" class="form-select form-select-sm shadow-sm rounded-pill border py-2 px-3">
                    <option value="">Todos</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmado" {{ request('estado') == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2 d-flex gap-2">
                <a href="{{ route('admin.turnos.index') }}" id="btnLimpiarFiltros" class="btn btn-sm btn-outline-secondary w-100 fw-bold rounded-pill py-2 shadow-sm">Limpiar</a>
            </div>
        </form>
    </div>
    
    @php
    $hoy = \Carbon\Carbon::today()->format('Y-m-d');
    $ahora = \Carbon\Carbon::now();

    $turnosHoy = $turnos->filter(function($t) use ($hoy, $ahora) {
        $esHoy = \Carbon\Carbon::parse($t->fecha)->format('Y-m-d') === $hoy;
        $noEstaTerminado = !in_array($t->estado, ['completado', 'cancelado']);
        $horaTurno = \Carbon\Carbon::parse($t->fecha . ' ' . $t->hora);
        $limiteTolerancia = (clone $horaTurno)->addHours(3);
        return $esHoy && $noEstaTerminado && $ahora->lte($limiteTolerancia);
    });

    $turnosFuturos = $turnos->filter(function($t) use ($hoy) {
        $esFuturo = \Carbon\Carbon::parse($t->fecha)->format('Y-m-d') > $hoy;
        $noEstaTerminado = !in_array($t->estado, ['completado', 'cancelado']);
        return $esFuturo && $noEstaTerminado;
    });

    $turnosFinalizados = $turnos->filter(function($t) use ($hoy, $ahora) {
        $fechaTurno = \Carbon\Carbon::parse($t->fecha)->format('Y-m-d');
        $esPasado = $fechaTurno < $hoy;
        $horaTurno = \Carbon\Carbon::parse($t->fecha . ' ' . $t->hora);
        $limiteTolerancia = (clone $horaTurno)->addHours(3);
        $esHoyYExcedioTolerancia = ($fechaTurno === $hoy && $ahora->gt($limiteTolerancia));
        $estaTerminado = in_array($t->estado, ['completado', 'cancelado']);
        return $esPasado || $esHoyYExcedioTolerancia || $estaTerminado;
    });

    $secciones = [
        ['titulo' => 'Turnos de Hoy', 'lista' => $turnosHoy, 'clase' => 'primary'],
        ['titulo' => 'Turnos Futuros', 'lista' => $turnosFuturos, 'clase' => 'info'],
        ['titulo' => 'Turnos Terminados / Vencidos', 'lista' => $turnosFinalizados, 'clase' => 'secondary']
    ];

    $hayTurnos = $turnos->isNotEmpty();
    @endphp

    @if($hayTurnos)
    @foreach($secciones as $seccion)
        @if($seccion['lista']->isNotEmpty())
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-family: 'Bebas Neue', sans-serif; font-size: 1.8rem; letter-spacing: 0.5px;">
                        <span class="badge bg-dark rounded-pill px-3 py-1 fs-6" style="background-color: #111827 !important;">
                            {{ $seccion['titulo'] }}
                        </span>
                        <span class="text-muted fs-6 fw-normal">({{ $seccion['lista']->count() }} citas)</span>
                    </h5>
                </div>
                
                @foreach($seccion['lista'] as $turno)
                    @php
                        $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora);
                        $duracionMinutos = $turno->servicios->isNotEmpty() 
                            ? $turno->servicios->sum('duracion') 
                            : ($turno->servicio->duracion ?? 30); 
                        
                        $finTurno = (clone $fechaHoraTurno)->addMinutes($duracionMinutos);
                        $limiteCompletar = (clone $finTurno)->addHours(3);
                        $yaEmpezoElTurno = $ahora->gte($fechaHoraTurno);
                        $excedioToleranciaCompletar = $ahora->gt($limiteCompletar);
                        $yaPasoLaHora = $ahora->gte($finTurno);
                        $esVencidoPasado = \Carbon\Carbon::parse($turno->fecha)->format('Y-m-d') < $hoy;

                        $badgeEstado = match($turno->estado) {
                            'completado' => 'bg-success-subtle text-success border border-success-subtle',
                            'cancelado'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                            'ausente'    => 'bg-dark text-white',
                            'confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold',
                            default      => 'bg-light text-dark border'
                        };
                    @endphp

                    <!-- VISTA MÓVIL -->
                    <div class="card border-0 shadow-sm rounded-4 mb-3 p-3 bg-white d-block d-md-none">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark border font-monospace px-2 py-1 mb-1">
                                    <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs
                                </span>
                                <h6 class="fw-bold text-dark mb-0">
                                    @if($turno->cliente_nombre_manual)
                                        <i class="bi bi-person me-1 text-secondary"></i> {{ $turno->cliente_nombre_manual }} <small class="text-muted fw-normal">(Mostrador)</small>
                                    @else
                                        <i class="bi bi-person-check text-success me-1"></i> {{ $turno->cliente->nombre ?? $turno->cliente->name ?? '-' }}
                                    @endif
                                </h6>
                            </div>
                            <div>
                                <span class="badge {{ $badgeEstado }} px-2 py-1 rounded-pill">{{ ucfirst($turno->estado) }}</span>
                            </div>
                        </div>

                        <div class="small text-muted mb-3 border-top border-bottom py-2 my-2">
                            <div class="row g-1">
                                <div class="col-6">
                                    <strong>Barbero:</strong> {{ $turno->barbero->nombre ?? $turno->barbero->name ?? '-' }}
                                </div>
                                <div class="col-6">
                                    <strong>Origen:</strong> 
                                    @if($turno->pago && $turno->pago->payment_id_mp)
                                        <span class="text-success fw-semibold">Online (MP)</span>
                                    @else
                                        <span>Mostrador</span>
                                    @endif
                                </div>
                                <div class="col-12 mt-1">
                                    <strong>Servicio:</strong> 
                                    @if($turno->servicios->isNotEmpty())
                                        {{ $turno->servicios->pluck('nombre')->implode(', ') }}
                                    @else
                                        {{ $turno->servicio->nombre ?? '-' }}
                                    @endif
                                </div>
                                <div class="col-12 mt-1">
                                    <strong>Sucursal:</strong> {{ $turno->sucursal->nombre ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            @if($esVencidoPasado || $turno->estado === 'completado' || $turno->estado === 'cancelado')
                                <span class="text-muted small fst-italic">Sin acciones</span>
                            @else
                                @if(in_array($turno->estado, ['pendiente', 'confirmado']) && $yaEmpezoElTurno && !$excedioToleranciaCompletar)
                                    <form action="{{ route('admin.turnos.completar', $turno->id) }}" method="POST" class="m-0 form-spinner" onsubmit="return confirm('¿Marcar este turno como completado?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold btn-action-spinner">
                                            <i class="bi bi-check-lg me-1"></i> Completar
                                        </button>
                                    </form>
                                @endif

                                @if($turno->pago && $turno->pago->payment_id_mp)
                                    {{-- Botón para Turno Online con Reembolso API MP --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-danger fw-bold rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalReembolsoOnline{{ $turno->id }}">
                                        <i class="bi bi-cash-coin"></i> Reembolsar
                                    </button>
                                @else
                                    {{-- CRUD Libre Desktop / Mostrador --}}
                                    @if(!$yaPasoLaHora)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold btn-editar-turno"
                                                data-id="{{ $turno->id }}"
                                                data-cliente="{{ $turno->cliente_nombre_manual ?? ($turno->cliente->nombre ?? $turno->cliente->name) }}"
                                                data-es-manual="{{ $turno->cliente_nombre_manual ? '1' : '0' }}"
                                                data-sucursal="{{ $turno->sucursal_id }}"
                                                data-barbero="{{ $turno->barbero_id }}"
                                                data-servicios="{{ json_encode($turno->servicios->isNotEmpty() ? $turno->servicios->pluck('id') : [$turno->servicio_id]) }}"
                                                data-fecha="{{ \Carbon\Carbon::parse($turno->fecha)->format('Y-m-d') }}"
                                                data-hora="{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}"
                                                data-estado="{{ $turno->estado }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarTurnoAdmin">
                                            <i class="bi bi-pencil me-1"></i> Editar
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.turnos.cancelarAdmin', $turno->id) }}" method="POST" class="m-0 form-spinner" onsubmit="return confirm('¿Estás seguro de cancelar este turno de mostrador?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold btn-action-spinner">
                                            <i class="bi bi-x-circle me-1"></i> Cancelar Turno
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- VISTA ESCRITORIO -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-white" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%);">
                                <tr>
                                    <th class="py-3 px-4">Fecha y Hora</th>
                                    <th class="py-3">Cliente</th>
                                    <th class="py-3">Origen / Pago</th>
                                    <th class="py-3">Barbero</th>
                                    <th class="py-3">Servicio</th>
                                    <th class="py-3">Sucursal</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($seccion['lista'] as $turno)
                                @php
                                    $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora);
                                    $duracionMinutos = $turno->servicios->isNotEmpty() ? $turno->servicios->sum('duracion') : ($turno->servicio->duracion ?? 30); 
                                    $finTurno = (clone $fechaHoraTurno)->addMinutes($duracionMinutos);
                                    $limiteCompletar = (clone $finTurno)->addHours(3);
                                    $yaEmpezoElTurno = $ahora->gte($fechaHoraTurno);
                                    $excedioToleranciaCompletar = $ahora->gt($limiteCompletar);
                                    $yaPasoLaHora = $ahora->gte($finTurno);
                                    $esVencidoPasado = \Carbon\Carbon::parse($turno->fecha)->format('Y-m-d') < $hoy;
                                    
                                    $badgeEstado = match($turno->estado) {
                                        'completado' => 'bg-success-subtle text-success border border-success-subtle',
                                        'cancelado'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        'ausente'    => 'bg-dark text-white',
                                        'confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold',
                                        default      => 'bg-light text-dark border'
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">
                                        <strong class="text-dark d-block">{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</strong>
                                        <span class="badge bg-light text-dark border mt-1 font-monospace px-2 py-1">
                                            {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if($turno->cliente_nombre_manual)
                                            <span class="fw-bold text-dark">
                                                <i class="bi bi-person me-1 text-secondary"></i> {{ $turno->cliente_nombre_manual }}
                                            </span>
                                            <br><small class="text-muted fst-italic">Mostrador</small>
                                        @else
                                            <span class="fw-bold text-dark">
                                                <i class="bi bi-person-check text-success me-1"></i> {{ $turno->cliente->nombre ?? $turno->cliente->name ?? '-' }}
                                            </span>
                                            @if(!empty($turno->cliente->telefono))
                                                <br><small class="text-muted">{{ $turno->cliente->telefono }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($turno->pago && $turno->pago->payment_id_mp)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-shield-check me-1"></i> Online (MP)
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                                <i class="bi bi-shop me-1"></i> Mostrador
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-semibold text-secondary">
                                            <i class="bi bi-scissors me-1"></i> {{ $turno->barbero->nombre ?? $turno->barbero->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-secondary fw-semibold small">
                                            @if($turno->servicios->isNotEmpty())
                                                {{ $turno->servicios->pluck('nombre')->implode(', ') }}
                                            @else
                                                {{ $turno->servicio->nombre ?? '-' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-muted small">{{ $turno->sucursal->nombre ?? '-' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge {{ $badgeEstado }} px-3 py-1 rounded-pill">{{ ucfirst($turno->estado) }}</span>
                                    </td>
                                    <td class="text-center align-middle py-3">
                                        @if($esVencidoPasado || $turno->estado === 'completado' || $turno->estado === 'cancelado')
                                            <span class="text-muted small fst-italic">Sin acciones</span>
                                        @else
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                @if(in_array($turno->estado, ['pendiente', 'confirmado']) && $yaEmpezoElTurno && !$excedioToleranciaCompletar)
                                                    <form action="{{ route('admin.turnos.completar', $turno->id) }}" method="POST" class="m-0 d-inline form-spinner" onsubmit="return confirm('¿Marcar este turno como completado?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-light text-success border shadow-sm rounded-circle p-1 d-flex align-items-center justify-content-center btn-action-spinner" style="width: 32px; height: 32px;" title="Completar Turno">
                                                            <i class="bi bi-check-lg fs-6"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($turno->pago && $turno->pago->payment_id_mp)
                                                    {{-- Botón para Turno Online con Reembolso API MP --}}
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger fw-bold rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-1" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalReembolsoOnline{{ $turno->id }}"
                                                            title="Cancelar Turno y Devolver Dinero Online">
                                                        <i class="bi bi-cash-coin"></i> <span class="small">Reembolsar</span>
                                                    </button>
                                                @else
                                                    {{-- CRUD Libre Desktop / Mostrador --}}
                                                    @if(!$yaPasoLaHora)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-light text-primary border shadow-sm rounded-circle p-1 d-flex align-items-center justify-content-center btn-editar-turno"
                                                                style="width: 32px; height: 32px;"
                                                                data-id="{{ $turno->id }}"
                                                                data-cliente="{{ $turno->cliente_nombre_manual ?? ($turno->cliente->nombre ?? $turno->cliente->name) }}"
                                                                data-es-manual="{{ $turno->cliente_nombre_manual ? '1' : '0' }}"
                                                                data-sucursal="{{ $turno->sucursal_id }}"
                                                                data-barbero="{{ $turno->barbero_id }}"
                                                                data-servicios="{{ json_encode($turno->servicios->isNotEmpty() ? $turno->servicios->pluck('id') : [$turno->servicio_id]) }}"
                                                                data-fecha="{{ \Carbon\Carbon::parse($turno->fecha)->format('Y-m-d') }}"
                                                                data-hora="{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }}"
                                                                data-estado="{{ $turno->estado }}"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalEditarTurnoAdmin"
                                                                title="Editar Turno">
                                                            <i class="bi bi-pencil fs-6"></i>
                                                        </button>
                                                    @endif

                                                    <form action="{{ route('admin.turnos.cancelarAdmin', $turno->id) }}" method="POST" class="m-0 d-inline form-spinner" onsubmit="return confirm('¿Estás seguro de cancelar este turno de mostrador?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-light text-danger border shadow-sm rounded-circle p-1 d-flex align-items-center justify-content-center btn-action-spinner" style="width: 32px; height: 32px;" title="Cancelar Turno de Mostrador">
                                                            <i class="bi bi-x-lg fs-6"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@else
    <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
        <div class="card-body">
            <i class="bi bi-calendar-x display-5 text-muted opacity-50 d-block mb-3"></i>
            <h5 class="fw-bold text-dark mb-1">No se encontraron turnos</h5>
            <p class="text-muted small mb-0">No hay registros que coincidan con los filtros seleccionados.</p>
        </div>
    </div>
@endif
</div>

<!-- ========================================================================= -->
<!-- MODAL DE NUEVO TURNO MANUAL -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalNuevoTurnoAdmin" tabindex="-1" aria-labelledby="modalNuevoTurnoAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            
            <div class="modal-header text-white px-4 py-3" style="background-color: #0f172a; border-bottom: 3px solid #0d9488;">
                <div>
                    <h3 class="fw-bold mb-1 fs-5 text-white">Registrar Turno Manual (Mostrador)</h3>
                    <p class="text-white-50 small mb-0">Completá los datos en orden estricto para asegurar disponibilidad</p>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.turnos.storeAdmin') }}" method="POST" id="formTurnoAdmin">
                @csrf
                <div class="modal-body p-4">

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-3 rounded-3 shadow-xs" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Tipo de Cliente -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">¿Cómo deseas cargar el cliente?</label>
                        <div class="btn-group w-100 shadow-sm rounded-3 overflow-hidden p-1 bg-light border" role="group">
                            <input type="radio" class="btn-check" name="tipo_cliente" id="tipoReg" value="registrado" {{ old('tipo_cliente', 'registrado') == 'registrado' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-teal py-2 fw-semibold fs-7 rounded-2" for="tipoReg">
                                <i class="bi bi-search me-1"></i> Cliente Registrado
                            </label>

                            <input type="radio" class="btn-check" name="tipo_cliente" id="tipoMan" value="manual" {{ old('tipo_cliente') == 'manual' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-teal py-2 fw-semibold fs-7 rounded-2" for="tipoMan">
                                <i class="bi bi-person-plus me-1"></i> No Registrado / Presencial
                            </label>
                        </div>
                    </div>

                    <!-- Buscador Registrado -->
                    <div class="mb-3 position-relative {{ old('tipo_cliente', 'registrado') == 'manual' ? 'd-none' : '' }}" id="box_cliente_registrado">
                        <label for="inputBusquedaCliente" class="form-label fw-semibold text-secondary">Buscar Cliente en el Sistema</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-search"></i></span>
                            <input type="text" id="inputBusquedaCliente" class="form-control form-control-lg fs-6 rounded-end-3" placeholder="Escribí nombre o email..." autocomplete="off">
                        </div>
                        <input type="hidden" name="cliente_id" id="modal_cliente_id" value="{{ old('cliente_id') }}">
                        <div id="listaResultadosClientes" class="list-group position-absolute w-100 shadow-lg d-none mt-1" style="z-index: 1056; max-height: 200px; overflow-y: auto; left: 0;"></div>
                    </div>

                    <!-- Cliente Manual -->
                    <div class="mb-3 {{ old('tipo_cliente') == 'manual' ? '' : 'd-none' }}" id="box_cliente_manual">
                        <label for="modal_cliente_nombre_manual" class="form-label fw-semibold text-secondary">Nombre / Alias del Cliente</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-person"></i></span>
                            <input type="text" name="cliente_nombre_manual" id="modal_cliente_nombre_manual" class="form-control form-control-lg fs-6 rounded-end-3" value="{{ old('cliente_nombre_manual', 'Cliente Ocasional') }}" placeholder="Ej: Juan Pérez" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios" required>
                        </div>
                    </div>

                    <!-- Sucursal -->
                    <div class="mb-3">
                        <label for="modal_sucursal_id" class="form-label fw-semibold text-secondary">Sucursal <span class="text-danger">*</span></label>
                        <select name="sucursal_id" id="modal_sucursal_id" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required>
                            <option value="">Seleccioná una sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Servicios -->
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold text-secondary">Servicios <span class="text-danger">*</span></label>
                        <div class="input-group mb-2 shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-scissors"></i></span>
                            <input type="text" id="inputBusquedaServicio" class="form-control form-control-lg fs-6" placeholder="Buscá y agregá uno o más servicios..." autocomplete="off">
                        </div>

                        <div id="serviciosSeleccionadosContainer" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <div id="inputsServiciosOcultos"></div>

                        <div id="listaResultadosServicios" class="list-group position-absolute w-100 shadow-lg d-none mt-1" style="z-index: 1056; max-height: 220px; overflow-y: auto; left: 0;"></div>
                        <div class="d-flex justify-content-between align-items-center mt-2 bg-light p-2 rounded-3 border">
                            <small class="text-secondary fw-medium">Duración estimada: <span id="minutosTotales" class="text-teal fw-bold">0</span> min</small>
                            <span class="fw-bold text-dark fs-6">Total a cobrar: $<span id="montoTotalMostrador" class="text-teal">0</span></span>
                        </div>
                    </div>

                    <!-- Barbero -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Barbero <span class="text-danger">*</span></label>
                        <input type="hidden" name="barbero_id" id="modal_barbero_id" value="{{ old('barbero_id') }}" required>
                        
                        <div id="barberosContainer" class="barberos-lista-horizontal d-flex align-items-center gap-3 overflow-auto py-2 px-2 text-muted small fst-italic rounded-3 bg-light border p-2 shadow-inner" style="scrollbar-width: none; min-height: 90px;">
                            Seleccioná sucursal y servicios primero.
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_fecha" class="form-label fw-semibold text-secondary">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="modal_fecha" class="form-control form-control-lg fs-6 rounded-3 shadow-sm" value="{{ old('fecha', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required disabled>
                        </div>

                        <!-- Hora -->
                        <div class="col-md-6 mb-3">
                            <label for="modal_hora" class="form-label fw-semibold text-secondary">Hora <span class="text-danger">*</span></label>
                            <select name="hora" id="modal_hora" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required disabled>
                                <option value="">Seleccioná un barbero y fecha</option>
                            </select>
                            <small class="text-muted d-block mt-1">Bloques validados sin choques</small>
                        </div>
                    </div>

                    <!-- Método de Pago -->
                    <div class="mb-3">
                        <label for="modal_metodo_pago" class="form-label fw-semibold text-secondary">Cobro en Mostrador</label>
                        <select name="metodo_pago" id="modal_metodo_pago" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required>
                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia_local" {{ old('metodo_pago') == 'transferencia_local' ? 'selected' : '' }}>Transferencia Local</option>
                            <option value="posnet_local" {{ old('metodo_pago') == 'posnet_local' ? 'selected' : '' }}>Posnet / Débito / Crédito</option>
                        </select>
                    </div>

                </div>
                
                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-lg py-2 rounded-3 px-4 shadow-xs" data-bs-dismiss="modal">Cancelar</button>
                    <!-- Botón con Validación AJAX Pre-flight y Tijeras -->
                    <button type="submit" id="btnGuardarTurno" class="btn btn-teal text-white btn-lg py-2 rounded-3 fw-bold shadow-sm px-4">Confirmar y Otorgar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL DE EDITAR TURNO -->
<!-- ========================================================================= -->
<div class="modal fade" id="modalEditarTurnoAdmin" tabindex="-1" aria-labelledby="modalEditarTurnoAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
            
            <div class="modal-header text-white px-4 py-3" style="background-color: #0f172a; border-bottom: 3px solid #0d9488;">
                <div>
                    <h3 class="fw-bold mb-1 fs-5 text-white">Editar Turno (Mostrador)</h3>
                    <p class="text-white-50 small mb-0">Modificá los datos del turno asignado</p>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarTurnoAdmin" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">

                    <div class="mb-3" id="edit_box_cliente_manual">
                        <label for="edit_cliente_nombre_manual" class="form-label fw-semibold text-secondary">Nombre / Alias del Cliente</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-person"></i></span>
                            <input type="text" name="cliente_nombre_manual" id="edit_cliente_nombre_manual" class="form-control form-control-lg fs-6 rounded-end-3" placeholder="Ej: Juan Pérez" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se permiten letras y espacios">
                        </div>
                    </div>

                    <div class="mb-3 d-none" id="edit_box_cliente_registrado">
                        <label class="form-label fw-semibold text-secondary">Cliente Registrado</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-person-check"></i></span>
                            <input type="text" id="edit_cliente_registrado_nombre" class="form-control form-control-lg fs-6 bg-light rounded-end-3" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_sucursal_id" class="form-label fw-semibold text-secondary">Sucursal <span class="text-danger">*</span></label>
                        <select name="sucursal_id" id="edit_sucursal_id" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required>
                            <option value="">Seleccioná una sucursal</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold text-secondary">Servicios <span class="text-danger">*</span></label>
                        <div class="input-group mb-2 shadow-sm">
                            <span class="input-group-text bg-white text-teal"><i class="bi bi-scissors"></i></span>
                            <input type="text" id="editInputBusquedaServicio" class="form-control form-control-lg fs-6" placeholder="Buscá y agregá uno o más servicios..." autocomplete="off">
                        </div>

                        <div id="editServiciosSeleccionadosContainer" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <div id="editInputsServiciosOcultos"></div>

                        <div id="editListaResultadosServicios" class="list-group position-absolute w-100 shadow-lg d-none mt-1" style="z-index: 1056; max-height: 220px; overflow-y: auto; left: 0;"></div>
                        <div class="d-flex justify-content-between align-items-center mt-2 bg-light p-2 rounded-3 border">
                            <small class="text-secondary fw-medium">Duración estimada: <span id="editMinutosTotales" class="text-teal fw-bold">0</span> min</small>
                            <span class="fw-bold text-dark fs-6">Total a cobrar: $<span id="editMontoTotalMostrador" class="text-teal">0</span></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Barbero <span class="text-danger">*</span></label>
                        <input type="hidden" name="barbero_id" id="edit_barbero_id" value="" required>
                        
                        <div id="editBarberosContainer" class="barberos-lista-horizontal d-flex align-items-center gap-3 overflow-auto py-2 px-2 text-muted small fst-italic rounded-3 bg-light border p-2 shadow-inner" style="scrollbar-width: none; min-height: 90px;">
                            Seleccioná sucursal y servicios primero.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_fecha" class="form-label fw-semibold text-secondary">Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="edit_fecha" class="form-control form-control-lg fs-6 rounded-3 shadow-sm" min="{{ date('Y-m-d') }}" required disabled>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_hora" class="form-label fw-semibold text-secondary">Hora <span class="text-danger">*</span></label>
                            <select name="hora" id="edit_hora" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required disabled>
                                <option value="">Seleccioná un barbero y fecha</option>
                            </select>
                            <small class="text-muted d-block mt-1">Bloques validados sin choques</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_estado" class="form-label fw-semibold text-secondary">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="edit_estado" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required>
                            <option value="confirmado">Confirmado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="completado">Completado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-lg py-2 rounded-3 px-4 shadow-xs" data-bs-dismiss="modal">Cancelar</button>
                    <!-- Botón de Edición con Tijeras -->
                    <button type="submit" id="btnGuardarEdicion" class="btn btn-teal text-white btn-lg py-2 rounded-3 fw-bold shadow-sm px-4">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pantalla de Carga Global con Animación de Tijeras -->
<div id="loadingOverlay">
    <div class="text-center p-4 rounded-4 shadow-lg bg-dark border border-secondary" style="max-width: 300px;">
        <div class="mb-3">
            <i class="bi bi-scissors text-teal animacion-tijera" style="font-size: 3.5rem !important;"></i>
        </div>
        <h5 class="fw-bold mb-1 text-white" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; font-size: 1.5rem;">Cargando...</h5>
        <p class="text-white-50 small mb-0">Actualizando listados y disponibilidad</p>
    </div>
</div>

<style>
    @keyframes tijeraCorte {
        0% { transform: rotate(0deg) scale(1); }
        25% { transform: rotate(-15deg) scale(1.1); }
        50% { transform: rotate(0deg) scale(1); }
        75% { transform: rotate(15deg) scale(1.1); }
        100% { transform: rotate(0deg) scale(1); }
    }

    .animacion-tijera {
        display: inline-block;
        animation: tijeraCorte 0.8s ease-in-out infinite;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.8);
        backdrop-filter: blur(5px);
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    #loadingOverlay.activo {
        opacity: 1;
        pointer-events: auto;
    }

    .barberos-lista-horizontal::-webkit-scrollbar { display: none; }
    .barbero-item-h { cursor: pointer; }
    .barbero-item-h:hover .barbero-img { border-color: #0d9488 !important; transform: scale(1.05); }
    .barbero-item-h.seleccionado .barbero-img { border-color: #0d9488 !important; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.3) !important; background-color: #f0fdfa !important; }
    .barbero-item-h.seleccionado .check-overlay { opacity: 1 !important; }
    .barbero-item-h.seleccionado .barbero-nombre-label { color: #0d9488 !important; font-weight: 700 !important; }
    
    .text-teal { color: #0d9488 !important; }
    .bg-teal { background-color: #0d9488 !important; }
    .bg-teal-soft { background-color: #f0fdfa !important; }
    
    .btn-outline-teal {
        color: #0d9488;
        border-color: #0d9488;
    }
    .btn-outline-teal:hover, .btn-check:checked + .btn-outline-teal {
        background-color: #0d9488 !important;
        color: #fff !important;
        border-color: #0d9488 !important;
    }

    #btnGuardarTurno:disabled,
    #btnGuardarEdicion:disabled {
        background-color: #e2e8f0 !important;
        border-color: #cbd5e1 !important;
        color: #475569 !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: inline-block !important;
    }

    #btnGuardarTurno:not(:disabled),
    #btnGuardarEdicion:not(:disabled) {
        background-color: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
        opacity: 1 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const loadingOverlay = document.getElementById('loadingOverlay');

    const formFiltros = document.getElementById('formFiltros');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');

    if (formFiltros && loadingOverlay) {
        formFiltros.querySelectorAll('select').forEach(select => {
            select.addEventListener('change', function() {
                loadingOverlay.classList.add('activo');
                formFiltros.submit();
            });
        });
    }

    if (btnLimpiarFiltros && loadingOverlay) {
        btnLimpiarFiltros.addEventListener('click', function(e) {
            e.preventDefault();
            loadingOverlay.classList.add('activo');
            window.location.href = this.href;
        });
    }

    const formTurnoAdmin = document.getElementById('formTurnoAdmin');
    const formEditarTurnoAdmin = document.getElementById('formEditarTurnoAdmin');
    const btnGuardarTurno = document.getElementById('btnGuardarTurno');
    const btnGuardarEdicion = document.getElementById('btnGuardarEdicion');

    if (formTurnoAdmin && loadingOverlay && btnGuardarTurno) {
        formTurnoAdmin.addEventListener('submit', function(e) {
            e.preventDefault();

            if (btnGuardarTurno.hasAttribute('disabled')) return;

            btnGuardarTurno.disabled = true;
            loadingOverlay.classList.add('activo');

            const barberoId = document.getElementById('modal_barbero_id').value;
            const sucursalId = document.getElementById('modal_sucursal_id').value;
            const fechaVal = document.getElementById('modal_fecha').value;
            const horaVal = document.getElementById('modal_hora').value;

            let duracionTotal = 30;
            if (typeof serviciosElegidos !== 'undefined' && serviciosElegidos.length > 0) {
                duracionTotal = serviciosElegidos.reduce((acc, s) => acc + parseInt(s.duracion || 30), 0);
            }

            fetch(`/turnos/horarios-disponibles?barbero_id=${barberoId}&sucursal_id=${sucursalId}&fecha=${fechaVal}&duracion_total=${duracionTotal}`)
                .then(res => res.json())
                .then(slots => {
                    const sigueLibre = slots.some(slot => slot.hora === horaVal || slot.hora.startsWith(horaVal));

                    if (sigueLibre) {
                        formTurnoAdmin.submit();
                    } else {
                        loadingOverlay.classList.remove('activo');
                        btnGuardarTurno.disabled = false;
                        alert('¡Atención! Alguien acaba de ocupar este horario hace un instante. Por favor, seleccioná otro.');
                        consultarHorariosDisponiblesAdmin(null);
                    }
                })
                .catch(() => {
                    formTurnoAdmin.submit();
                });
        });
    }

    if (formEditarTurnoAdmin && loadingOverlay && btnGuardarEdicion) {
        formEditarTurnoAdmin.addEventListener('submit', function(e) {
            if (btnGuardarEdicion.hasAttribute('disabled')) {
                e.preventDefault();
                return false;
            }
            btnGuardarEdicion.disabled = true;
            loadingOverlay.classList.add('activo');
        });
    }

    document.querySelectorAll('.form-spinner').forEach(form => {
        form.addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-action-spinner');
            if (btn) {
                if (btn.hasAttribute('disabled')) {
                    e.preventDefault();
                    return false;
                }
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
            }
        });
    });

    const modalNuevoTurno = document.getElementById('modalNuevoTurnoAdmin');
    const modalSucursal = document.getElementById('modal_sucursal_id');

    if (modalNuevoTurno && modalSucursal) {
        modalNuevoTurno.addEventListener('shown.bs.modal', function () {
            if (modalSucursal.options.length > 1 && !modalSucursal.value) {
                modalSucursal.value = modalSucursal.options[1].value;
                modalSucursal.dispatchEvent(new Event('change'));
            }
        });
    }

    const sucursalSelect = document.getElementById('sucursal_id');
    const barberoSelect = document.getElementById('barbero_id');
    
    if (barberoSelect && sucursalSelect) {
        const todasLasOpcionesBarberos = Array.from(barberoSelect.options);

        function actualizarBarberosLocales() {
            const sucursalId = sucursalSelect.value;
            const barberoSeleccionadoActual = barberoSelect.value;

            barberoSelect.innerHTML = '';
            const optionDefault = document.createElement('option');
            optionDefault.value = '';
            optionDefault.textContent = 'Todos los barberos';
            barberoSelect.appendChild(optionDefault);

            todasLasOpcionesBarberos.forEach(option => {
                if (option.value === '') return;
                const sucursalDelBarbero = option.getAttribute('data-sucursal');
                if (!sucursalId || sucursalDelBarbero === sucursalId) {
                    barberoSelect.appendChild(option);
                }
            });

            barberoSelect.value = barberoSeleccionadoActual;
        }

        sucursalSelect.addEventListener('change', function() {
            actualizarBarberosLocales();
        });
    }

    const radioReg = document.getElementById('tipoReg');
    const radioMan = document.getElementById('tipoMan');
    const boxReg = document.getElementById('box_cliente_registrado');
    const boxMan = document.getElementById('box_cliente_manual');
    const inputBusqueda = document.getElementById('inputBusquedaCliente');
    const hiddenClienteId = document.getElementById('modal_cliente_id');
    const inputManual = document.getElementById('modal_cliente_nombre_manual');

    if (inputManual) {
        inputManual.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    const editInputManual = document.getElementById('edit_cliente_nombre_manual');
    if (editInputManual) {
        editInputManual.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    if (radioReg && radioMan) {
        radioReg.addEventListener('change', function() {
            if (this.checked) {
                boxReg.classList.remove('d-none');
                boxMan.classList.add('d-none');
                inputManual.value = '';
            }
        });

        radioMan.addEventListener('change', function() {
            if (this.checked) {
                boxMan.classList.remove('d-none');
                boxReg.classList.add('d-none');
                inputBusqueda.value = '';
                hiddenClienteId.value = '';
                if (!inputManual.value.trim()) {
                    inputManual.value = 'Cliente Ocasional';
                }
            }
        });
    }

    const clientesDelSistema = @json($clientes ?? []);
    const listaResultadosClientes = document.getElementById('listaResultadosClientes');

    if (inputBusqueda && listaResultadosClientes) {
        inputBusqueda.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            listaResultadosClientes.innerHTML = '';
            hiddenClienteId.value = '';

            if (query.length === 0) {
                listaResultadosClientes.classList.add('d-none');
                return;
            }

            const filtrados = clientesDelSistema.filter(c => {
                const nombre = (c.nombre || c.name || '').toLowerCase();
                const email = (c.email || '').toLowerCase();
                return nombre.includes(query) || email.includes(query);
            });

            if (filtrados.length > 0) {
                listaResultadosClientes.classList.remove('d-none');
                filtrados.forEach(c => {
                    const nombreCliente = c.nombre || c.name;
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2 small';
                    item.innerHTML = `<strong>${nombreCliente}</strong><br><span class="text-muted">${c.email}</span>`;
                    
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        inputBusqueda.value = nombreCliente;
                        hiddenClienteId.value = c.id;
                        listaResultadosClientes.classList.add('d-none');
                    });
                    listaResultadosClientes.appendChild(item);
                });
            } else {
                listaResultadosClientes.classList.remove('d-none');
                listaResultadosClientes.innerHTML = `<div class="list-group-item text-muted small py-2">No se encontraron clientes</div>`;
            }
        });
    }

    const serviciosDelSistema = @json($servicios ?? []);
    const inputBusquedaServicio = document.getElementById('inputBusquedaServicio');
    const listaResultadosServicios = document.getElementById('listaResultadosServicios');
    const serviciosContainer = document.getElementById('serviciosSeleccionadosContainer');
    const inputsOcultosContainer = document.getElementById('inputsServiciosOcultos');
    const spanMinutosTotales = document.getElementById('minutosTotales');
    const spanMontoTotal = document.getElementById('montoTotalMostrador');

    let serviciosElegidos = [];

    if (inputBusquedaServicio && listaResultadosServicios) {
        inputBusquedaServicio.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            listaResultadosServicios.innerHTML = '';

            if (query.length === 0) {
                listaResultadosServicios.classList.add('d-none');
                return;
            }

            const filtrados = serviciosDelSistema.filter(s => {
                const coincideNombre = (s.nombre || '').toLowerCase().includes(query);
                const yaSeleccionado = serviciosElegidos.some(sel => sel.id === s.id);
                const mismoTipoYaElegido = serviciosElegidos.some(sel => sel.tipo_servicio_id && sel.tipo_servicio_id === s.tipo_servicio_id);
                return coincideNombre && !yaSeleccionado && !mismoTipoYaElegido;
            });

            if (filtrados.length > 0) {
                listaResultadosServicios.classList.remove('d-none');
                filtrados.forEach(s => {
                    const precio = s.precio_actual ?? s.precio ?? 0;
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2 small';
                    item.innerHTML = `<strong>${s.nombre}</strong> <span class="text-muted">($${precio} - ${s.duracion} min)</span>`;
                    
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        agregarServicio(s);
                        inputBusquedaServicio.value = '';
                        listaResultadosServicios.classList.add('d-none');
                    });
                    listaResultadosServicios.appendChild(item);
                });
            } else {
                listaResultadosServicios.classList.remove('d-none');
                listaResultadosServicios.innerHTML = `<div class="list-group-item text-muted small py-2">No disponible</div>`;
            }
        });
    }

    function agregarServicio(servicio) {
        serviciosElegidos.push(servicio);
        renderizarServiciosElegidos();
        verificarYMostrarBarberosAdmin();
    }

    window.quitarServicio = function(id) {
        serviciosElegidos = serviciosElegidos.filter(s => s.id !== id);
        renderizarServiciosElegidos();
        verificarYMostrarBarberosAdmin();
    }

    function renderizarServiciosElegidos() {
        if (!serviciosContainer) return;
        serviciosContainer.innerHTML = '';
        inputsOcultosContainer.innerHTML = '';
        
        let duracionTotal = 0;
        let precioTotal = 0;

        serviciosElegidos.forEach(s => {
            duracionTotal += parseInt(s.duracion || 30);
            precioTotal += parseFloat(s.precio_actual ?? s.precio ?? 0);

            const badge = document.createElement('span');
            badge.className = 'badge bg-teal text-white p-2 d-inline-flex align-items-center gap-2 fs-7';
            badge.innerHTML = `${s.nombre} ($${s.precio_actual ?? s.precio} - ${s.duracion} min) <button type="button" class="btn-close btn-close-white btn-xs" style="font-size: 0.65rem;" onclick="quitarServicio(${s.id})"></button>`;
            serviciosContainer.appendChild(badge);

            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'servicios[]';
            inputHidden.value = s.id;
            inputsOcultosContainer.appendChild(inputHidden);
        });

        if (spanMinutosTotales) spanMinutosTotales.textContent = duracionTotal;
        if (spanMontoTotal) spanMontoTotal.textContent = precioTotal.toFixed(2);
    }

    const fechaInput = document.getElementById('modal_fecha');
    const horaInput = document.getElementById('modal_hora');
    const barberosContainer = document.getElementById('barberosContainer');
    const inputBarberoId = document.getElementById('modal_barbero_id');
    const btnGuardar = document.getElementById('btnGuardarTurno');

    let cacheBarberosSucursal = [];
    let hoyStr = new Date().toISOString().split('T')[0];

    if (fechaInput) {
        fechaInput.min = hoyStr;
    }

    if (modalSucursal) {
        modalSucursal.addEventListener('change', function() {
            let sucursalId = this.value;
            resetearCamposDesdeSucursal();
            cacheBarberosSucursal = [];

            if (sucursalId) {
                fetch('/barberos/' + sucursalId)
                    .then(res => res.json())
                    .then(data => { 
                        cacheBarberosSucursal = data || []; 
                        verificarYMostrarBarberosAdmin();
                    })
                    .catch(() => { cacheBarberosSucursal = []; });
            }
        });
    }

    function resetearCamposDesdeSucursal() {
        if (inputBarberoId) inputBarberoId.value = '';
        if (fechaInput) {
            fechaInput.value = '';
            fechaInput.disabled = true;
        }
        if (horaInput) {
            horaInput.innerHTML = '<option value="">Seleccioná un barbero y fecha</option>';
            horaInput.disabled = true;
        }
        if (btnGuardar) btnGuardar.disabled = true;
        if (barberosContainer) barberosContainer.innerHTML = '<span class="text-muted fst-italic">Seleccioná una sucursal y al menos un servicio.</span>';
    }

    function verificarYMostrarBarberosAdmin() {
        if (!modalSucursal || !barberosContainer) return;
        let sucursalId = modalSucursal.value;
        
        if (!sucursalId || serviciosElegidos.length === 0) {
            barberosContainer.innerHTML = '<span class="text-muted fst-italic">Seleccioná sucursal y al menos un servicio.</span>';
            if (fechaInput) {
                fechaInput.value = '';
                fechaInput.disabled = true;
            }
            return;
        }

        if (cacheBarberosSucursal.length === 0) {
            barberosContainer.innerHTML = '<span class="text-danger fst-italic">No hay profesionales disponibles en esta sucursal.</span>';
            return;
        }

        let duracionTotal = serviciosElegidos.reduce((acc, s) => acc + parseInt(s.duracion || 30), 0);
        if (duracionTotal === 0) duracionTotal = 30;

        fetch(`/turnos/barbero-sugerido?sucursal_id=${sucursalId}&fecha=${hoyStr}&duracion_total=${duracionTotal}`)
            .then(res => res.json())
            .then(sugerencia => {
                let barberoSugeridoId = sugerencia.barbero_id;
                let horaSugerida = sugerencia.hora;

                if (!barberoSugeridoId && cacheBarberosSucursal.length > 0) {
                    barberoSugeridoId = cacheBarberosSucursal[0].id;
                }

                renderizarHTMLBarberosAdmin(barberoSugeridoId);

                if (barberoSugeridoId) {
                    if (inputBarberoId) inputBarberoId.value = barberoSugeridoId;
                    
                    if (fechaInput) {
                        fechaInput.value = hoyStr;
                        fechaInput.disabled = false;
                        
                        consultarHorariosDisponiblesAdmin(horaSugerida);
                    }
                }
            })
            .catch(() => {
                let fallbackBarbero = cacheBarberosSucursal[0] ? cacheBarberosSucursal[0].id : null;
                renderizarHTMLBarberosAdmin(fallbackBarbero);
                if (fallbackBarbero && inputBarberoId) inputBarberoId.value = fallbackBarbero;
                if (fechaInput) {
                    fechaInput.value = hoyStr;
                    fechaInput.disabled = false;
                    consultarHorariosDisponiblesAdmin(null);
                }
            });
    }

    function renderizarHTMLBarberosAdmin(barberoARestaurar) {
        let html = '';
        cacheBarberosSucursal.forEach(barbero => {
            let iniciales = barbero.nombre.substring(0, 2).toUpperCase();
            let primerNombre = barbero.nombre.trim().split(' ')[0];
            let fotoPerfil = barbero.avatar || barbero.imagen;
            let imgHtml = '';

            if (fotoPerfil) {
                imgHtml = `<img src="/storage/${fotoPerfil}" alt="${barbero.nombre}" class="rounded-circle object-fit-cover barbero-img shadow-xs" style="width: 60px; height: 60px; border: 2px solid #e2e8f0; transition: all 0.2s ease;">`;
            } else {
                imgHtml = `<div class="rounded-circle bg-teal-soft text-teal d-flex align-items-center justify-content-center fw-bold barbero-img shadow-xs mx-auto" style="width: 60px; height: 60px; font-size: 1rem; border: 2px solid #ccfbf1; transition: all 0.2s ease;">${iniciales}</div>`;
            }

            let seleccionado = (barberoARestaurar && barberoARestaurar == barbero.id) ? 'seleccionado' : '';

            html += `
                <div class="barbero-item-h text-center ${seleccionado}" 
                     data-id="${barbero.id}" 
                     style="cursor: pointer; flex: 0 0 auto; width: 75px; transition: transform 0.2s ease;">
                    <div class="avatar-wrapper position-relative d-inline-block mb-1">
                        ${imgHtml}
                        <div class="check-overlay position-absolute top-0 start-0 w-100 h-100 rounded-circle bg-teal bg-opacity-75 d-flex align-items-center justify-content-center text-white opacity-0 transition-opacity" style="transition: opacity 0.2s ease;">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                    </div>
                    <span class="d-block text-dark fw-semibold text-truncate barbero-nombre-label" style="font-size: 0.72rem; line-height: 1.2;" title="${barbero.nombre}">
                        ${primerNombre}
                    </span>
                </div>
            `;
        });
        barberosContainer.innerHTML = html;
        
        document.querySelectorAll('#barberosContainer .barbero-item-h').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('#barberosContainer .barbero-item-h').forEach(i => i.classList.remove('seleccionado'));
                this.classList.add('seleccionado');
                if (inputBarberoId) inputBarberoId.value = this.getAttribute('data-id');
                
                if (fechaInput) {
                    fechaInput.disabled = false;
                    if (fechaInput.value) {
                        consultarHorariosDisponiblesAdmin(null);
                    }
                }
            });
        });
    }

    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            if (this.value && inputBarberoId && inputBarberoId.value) {
                consultarHorariosDisponiblesAdmin(null);
            }
        });
    }

    window.consultarHorariosDisponiblesAdmin = function(horaARestaurar) {
        let barberoId = inputBarberoId.value;
        let sucursalId = modalSucursal.value;
        let fechaVal = fechaInput.value;
        let duracionTotal = serviciosElegidos.reduce((acc, s) => acc + parseInt(s.duracion || 30), 0);

        if (!barberoId || !sucursalId || !fechaVal || duracionTotal === 0) return;

        horaInput.innerHTML = '<option value="">⏳ Buscando horarios disponibles...</option>';
        horaInput.disabled = true;

        fetch(`/turnos/horarios-disponibles?barbero_id=${barberoId}&sucursal_id=${sucursalId}&fecha=${fechaVal}&duracion_total=${duracionTotal}`)
            .then(res => res.json())
            .then(slots => {
                horaInput.innerHTML = '<option value="">Seleccioná un horario</option>';
                if (slots.length > 0) {
                    slots.forEach((slot, index) => {
                        let opt = document.createElement('option');
                        opt.value = slot.hora;
                        opt.textContent = slot.formato;
                        
                        if ((horaARestaurar && slot.hora === horaARestaurar) || (!horaARestaurar && index === 0)) {
                            opt.selected = true;
                        }
                        horaInput.appendChild(opt);
                    });
                    horaInput.disabled = false;
                    if (btnGuardar) btnGuardar.disabled = false;
                } else {
                    horaInput.innerHTML = '<option value="">No hay baches libres para este barbero en esta fecha</option>';
                    horaInput.disabled = true;
                    if (btnGuardar) btnGuardar.disabled = true;
                }
            })
            .catch(() => {
                horaInput.innerHTML = '<option value="">Error al cargar horarios</option>';
            });
    }

    if (horaInput) {
        horaInput.addEventListener('change', function() {
            if (inputBarberoId && inputBarberoId.value && fechaInput && fechaInput.value && this.value && serviciosElegidos.length > 0) {
                if (btnGuardar) btnGuardar.disabled = false;
            } else {
                if (btnGuardar) btnGuardar.disabled = true;
            }
        });
    }

    const editSucursal = document.getElementById('edit_sucursal_id');
    const editBarberoId = document.getElementById('edit_barbero_id');
    const editBarberosContainer = document.getElementById('editBarberosContainer');
    const editFecha = document.getElementById('edit_fecha');
    const editHora = document.getElementById('edit_hora');
    const editEstado = document.getElementById('edit_estado');

    const editInputBusquedaServicio = document.getElementById('editInputBusquedaServicio');
    const editListaResultadosServicios = document.getElementById('editListaResultadosServicios');
    const editServiciosContainer = document.getElementById('editServiciosSeleccionadosContainer');
    const editInputsOcultosContainer = document.getElementById('editInputsServiciosOcultos');
    const editSpanMinutos = document.getElementById('editMinutosTotales');
    const editSpanMonto = document.getElementById('editMontoTotalMostrador');

    let serviciosElegidosEdit = [];
    let cacheBarberosEdit = [];

    if (editFecha) {
        editFecha.min = new Date().toISOString().split('T')[0];
    }

    const botonesEditar = document.querySelectorAll('.btn-editar-turno');
    botonesEditar.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const cliente = this.getAttribute('data-cliente');
            const esManual = this.getAttribute('data-es-manual') === '1';
            const sucursalId = this.getAttribute('data-sucursal');
            const barberoId = this.getAttribute('data-barbero');
            
            let idsServicios = [];
            try {
                idsServicios = JSON.parse(this.getAttribute('data-servicios') || '[]');
            } catch(e) {
                const servicioSimple = this.getAttribute('data-servicio');
                idsServicios = servicioSimple ? [parseInt(servicioSimple)] : [];
            }

            const fecha = this.getAttribute('data-fecha');
            const hora = this.getAttribute('data-hora');
            const estado = this.getAttribute('data-estado');

            const form = document.getElementById('formEditarTurnoAdmin');
            if (form) form.action = `/admin/turnos/${id}/admin`;

            const boxManual = document.getElementById('edit_box_cliente_manual');
            const boxRegistrado = document.getElementById('edit_box_cliente_registrado');
            const inputManualEdit = document.getElementById('edit_cliente_nombre_manual');
            const inputRegistrado = document.getElementById('edit_cliente_registrado_nombre');

            if (esManual) {
                if(boxManual) boxManual.classList.remove('d-none');
                if(boxRegistrado) boxRegistrado.classList.add('d-none');
                if(inputManualEdit) inputManualEdit.value = cliente || '';
            } else {
                if(boxManual) boxManual.classList.add('d-none');
                if(boxRegistrado) boxRegistrado.classList.remove('d-none');
                if(inputRegistrado) inputRegistrado.value = cliente || '';
            }

            if(editSucursal) editSucursal.value = sucursalId || '';
            if(editBarberoId) editBarberoId.value = barberoId || '';
            if(editEstado) editEstado.value = estado || '';
            if(editFecha) {
                editFecha.value = fecha || '';
                editFecha.disabled = false;
            }

            serviciosElegidosEdit = [];
            idsServicios.forEach(sId => {
                const servicioEncontrado = serviciosDelSistema.find(s => s.id == sId);
                if (servicioEncontrado) {
                    serviciosElegidosEdit.push(servicioEncontrado);
                }
            });
            renderizarServiciosEdit();

            if (sucursalId) {
                fetch('/barberos/' + sucursalId)
                    .then(res => res.json())
                    .then(data => {
                        cacheBarberosEdit = data || [];
                        renderBarberosEdit(barberoId);
                        if (barberoId && fecha) {
                            cargarHorariosEdit(barberoId, sucursalId, fecha, hora);
                        }
                    })
                    .catch(() => { cacheBarberosEdit = []; });
            }
        });
    });

    if (editSucursal) {
        editSucursal.addEventListener('change', function() {
            let sucursalId = this.value;
            if(editBarberoId) editBarberoId.value = '';
            if(editFecha) {
                editFecha.value = '';
                editFecha.disabled = true;
            }
            if(editHora) {
                editHora.innerHTML = '<option value="">Seleccioná un barbero y fecha</option>';
                editHora.disabled = true;
            }
            if(editBarberosContainer) editBarberosContainer.innerHTML = '<span class="text-muted fst-italic">Seleccioná sucursal y al menos un servicio.</span>';
            cacheBarberosEdit = [];

            if (sucursalId) {
                fetch('/barberos/' + sucursalId)
                    .then(res => res.json())
                    .then(data => {
                        cacheBarberosEdit = data || [];
                        renderBarberosEdit('');
                    })
                    .catch(() => { cacheBarberosEdit = []; });
            }
        });
    }

    if (editInputBusquedaServicio && editListaResultadosServicios) {
        editInputBusquedaServicio.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            editListaResultadosServicios.innerHTML = '';

            if (query.length === 0) {
                editListaResultadosServicios.classList.add('d-none');
                return;
            }

            const filtrados = serviciosDelSistema.filter(s => {
                const coincideNombre = (s.nombre || '').toLowerCase().includes(query);
                const yaSeleccionado = serviciosElegidosEdit.some(sel => sel.id === s.id);
                const mismoTipoYaElegido = serviciosElegidosEdit.some(sel => sel.tipo_servicio_id && sel.tipo_servicio_id === s.tipo_servicio_id);
                return coincideNombre && !yaSeleccionado && !mismoTipoYaElegido;
            });

            if (filtrados.length > 0) {
                editListaResultadosServicios.classList.remove('d-none');
                filtrados.forEach(s => {
                    const precio = s.precio_actual ?? s.precio ?? 0;
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action py-2 small';
                    item.innerHTML = `<strong>${s.nombre}</strong> <span class="text-muted">($${precio} - ${s.duracion} min)</span>`;
                    
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        serviciosElegidosEdit.push(s);
                        renderizarServiciosEdit();
                        renderBarberosEdit(editBarberoId.value);
                        editInputBusquedaServicio.value = '';
                        editListaResultadosServicios.classList.add('d-none');
                    });
                    editListaResultadosServicios.appendChild(item);
                });
            } else {
                editListaResultadosServicios.classList.remove('d-none');
                editListaResultadosServicios.innerHTML = `<div class="list-group-item text-muted small py-2">No disponible</div>`;
            }
        });
    }

    window.quitarServicioEdit = function(id) {
        serviciosElegidosEdit = serviciosElegidosEdit.filter(s => s.id !== id);
        renderizarServiciosEdit();
        renderBarberosEdit(editBarberoId.value);
    }

    function renderizarServiciosEdit() {
        if (!editServiciosContainer) return;
        editServiciosContainer.innerHTML = '';
        editInputsOcultosContainer.innerHTML = '';
        
        let duracionTotal = 0;
        let precioTotal = 0;

        serviciosElegidosEdit.forEach(s => {
            duracionTotal += parseInt(s.duracion || 30);
            precioTotal += parseFloat(s.precio_actual ?? s.precio ?? 0);

            const badge = document.createElement('span');
            badge.className = 'badge bg-teal text-white p-2 d-inline-flex align-items-center gap-2 fs-7';
            badge.innerHTML = `${s.nombre} ($${s.precio_actual ?? s.precio} - ${s.duracion} min) <button type="button" class="btn-close btn-close-white btn-xs" style="font-size: 0.65rem;" onclick="quitarServicioEdit(${s.id})"></button>`;
            editServiciosContainer.appendChild(badge);

            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'servicios[]';
            inputHidden.value = s.id;
            editInputsOcultosContainer.appendChild(inputHidden);
        });

        if (editSpanMinutos) editSpanMinutos.textContent = duracionTotal;
        if (editSpanMonto) editSpanMonto.textContent = precioTotal.toFixed(2);
    }

    function renderBarberosEdit(barberoSeleccionadoId) {
        if (!editBarberosContainer) return;
        let sucursalId = editSucursal.value;

        if (!sucursalId || serviciosElegidosEdit.length === 0) {
            editBarberosContainer.innerHTML = '<span class="text-muted fst-italic">Seleccioná sucursal y al menos un servicio.</span>';
            return;
        }

        if (cacheBarberosEdit.length === 0) {
            editBarberosContainer.innerHTML = '<span class="text-danger fst-italic">No hay profesionales disponibles.</span>';
            return;
        }

        let html = '';
        cacheBarberosEdit.forEach(barbero => {
            let iniciales = barbero.nombre.substring(0, 2).toUpperCase();
            let primerNombre = barbero.nombre.trim().split(' ')[0];
            let fotoPerfil = barbero.avatar || barbero.imagen;
            let imgHtml = '';

            if (fotoPerfil) {
                imgHtml = `<img src="/storage/${fotoPerfil}" alt="${barbero.nombre}" class="rounded-circle object-fit-cover barbero-img shadow-xs" style="width: 60px; height: 60px; border: 2px solid #e2e8f0; transition: all 0.2s ease;">`;
            } else {
                imgHtml = `<div class="rounded-circle bg-teal-soft text-teal d-flex align-items-center justify-content-center fw-bold barbero-img shadow-xs mx-auto" style="width: 60px; height: 60px; font-size: 1rem; border: 2px solid #ccfbf1; transition: all 0.2s ease;">${iniciales}</div>`;
            }

            let seleccionado = (barberoSeleccionadoId == barbero.id) ? 'seleccionado' : '';

            html += `
                <div class="barbero-item-h text-center ${seleccionado}" 
                     data-id="${barbero.id}" 
                     style="cursor: pointer; flex: 0 0 auto; width: 75px; transition: transform 0.2s ease;">
                    <div class="avatar-wrapper position-relative d-inline-block mb-1">
                        ${imgHtml}
                        <div class="check-overlay position-absolute top-0 start-0 w-100 h-100 rounded-circle bg-teal bg-opacity-75 d-flex align-items-center justify-content-center text-white opacity-0 transition-opacity" style="transition: opacity 0.2s ease;">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                    </div>
                    <span class="d-block text-dark fw-semibold text-truncate barbero-nombre-label" style="font-size: 0.72rem; line-height: 1.2;" title="${barbero.nombre}">
                        ${primerNombre}
                    </span>
                </div>
            `;
        });
        editBarberosContainer.innerHTML = html;

        editBarberosContainer.querySelectorAll('.barbero-item-h').forEach(item => {
            item.addEventListener('click', function() {
                editBarberosContainer.querySelectorAll('.barbero-item-h').forEach(i => i.classList.remove('seleccionado'));
                this.classList.add('seleccionado');
                if (editBarberoId) editBarberoId.value = this.getAttribute('data-id');
                
                if (editFecha) {
                    editFecha.disabled = false;
                    if (editFecha.value) {
                        cargarHorariosEdit(editBarberoId.value, editSucursal.value, editFecha.value, '');
                    }
                }
            });
        });
    }

    if (editFecha) {
        editFecha.addEventListener('change', function() {
            if (this.value && editBarberoId && editBarberoId.value) {
                cargarHorariosEdit(editBarberoId.value, editSucursal.value, this.value, '');
            }
        });
    }

    function cargarHorariosEdit(barberoId, sucursalId, fechaVal, horaActual) {
        let duracionTotal = serviciosElegidosEdit.reduce((acc, s) => acc + parseInt(s.duracion || 30), 0);
        if (!barberoId || !sucursalId || !fechaVal || duracionTotal === 0) return;

        if(editHora) {
            editHora.innerHTML = '<option value="">⏳ Buscando horarios...</option>';
            editHora.disabled = true;
        }

        fetch(`/turnos/horarios-disponibles?barbero_id=${barberoId}&sucursal_id=${sucursalId}&fecha=${fechaVal}&duracion_total=${duracionTotal}`)
            .then(res => res.json())
            .then(slots => {
                if(!editHora) return;
                editHora.innerHTML = '<option value="">Seleccioná un horario</option>';
                let horaEncontrada = false;
                if (slots.length > 0) {
                    slots.forEach(slot => {
                        let opt = document.createElement('option');
                        opt.value = slot.hora;
                        opt.textContent = slot.formato;
                        if (slot.hora === horaActual || slot.hora.startsWith(horaActual)) {
                            opt.selected = true;
                            horaEncontrada = true;
                        }
                        editHora.appendChild(opt);
                    });
                }
                if (!horaEncontrada && horaActual) {
                    let optExtra = document.createElement('option');
                    optExtra.value = horaActual;
                    optExtra.textContent = horaActual + ' (Actual)';
                    optExtra.selected = true;
                    editHora.appendChild(optExtra);
                }
                editHora.disabled = false;
            })
            .catch(() => {
                if(editHora) {
                    editHora.innerHTML = '<option value="">Error al cargar horarios</option>';
                }
            });
    }
});
</script>
@endsection