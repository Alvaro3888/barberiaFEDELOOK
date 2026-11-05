@php
    $secciones = [
        ['titulo' => 'Turnos de Hoy', 'lista' => $turnosHoy, 'clase' => 'primary', 'badgeColor' => '#168F87'],
        ['titulo' => 'Turnos Futuros', 'lista' => $turnosFuturos, 'clase' => 'info', 'badgeColor' => '#212529'],
        ['titulo' => 'Turnos Terminados / Vencidos', 'lista' => $turnosFinalizados, 'clase' => 'secondary', 'badgeColor' => '#6c757d']
    ];
    $hayTurnos = $turnosHoy->isNotEmpty() || $turnosFuturos->isNotEmpty() || $turnosFinalizados->isNotEmpty();
    $ahora = \Carbon\Carbon::now();
@endphp

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

@if($hayTurnos)
    @foreach($secciones as $seccion)
        @if($seccion['lista']->isNotEmpty())
            <div class="mb-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge px-3 py-2 rounded-pill fw-bold shadow-sm text-white" style="background-color: {{ $seccion['badgeColor'] }}; font-size: 0.85rem; letter-spacing: 0.5px;">
                        {{ $seccion['titulo'] }} ({{ $seccion['lista']->count() }})
                    </span>
                    <div class="flex-grow-1 border-bottom border-secondary opacity-10"></div>
                </div>

                @php
                    $turnosData = $seccion['lista'];
                @endphp

                <!-- ========================================== -->
                <!-- 🖥️ VISTA DESKTOP (Tabla Moderna y Limpia) -->
                <!-- ========================================== -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white d-none d-lg-block mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="text-white" style="background-color: #111;">
                                <tr>
                                    <th class="py-3 px-3">Fecha y Hora</th>
                                    <th class="py-3">Cliente</th>
                                    <th class="py-3">Origen / Pago</th>
                                    <th class="py-3">Servicio</th>
                                    <th class="py-3">Sucursal</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-center">Acciones Operativas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($turnosData as $turno)
                                @php
                                    $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora);
                                    $duracionMinutos = $turno->servicios->isNotEmpty() 
                                        ? $turno->servicios->sum('duracion') 
                                        : ($turno->servicio->duracion ?? 30);
                                    
                                    $finTurno = (clone $fechaHoraTurno)->addMinutes($duracionMinutos);
                                    $esTerminado = in_array($turno->estado, ['completado', 'cancelado', 'ausente']);
                                    $esVencido = $ahora->gt($finTurno) && !$esTerminado;
                                    $dentroDeVentana = $ahora->gte($fechaHoraTurno) && $ahora->lte((clone $finTurno)->addHours(3));
                                    $puedeOperar = in_array($turno->estado, ['pendiente', 'confirmado']) && $dentroDeVentana;
                                @endphp
                                <tr>
                                    <td class="px-3">
                                        <strong class="text-dark">{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</strong>
                                        <br>
                                        <span class="badge bg-light text-dark border mt-1 font-monospace">{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs</span>
                                    </td>
                                    <td>
                                        @if(!empty($turno->cliente_nombre_manual))
                                            <span class="fw-bold text-dark">
                                                <i class="bi bi-person me-1 text-secondary"></i> {{ $turno->cliente_nombre_manual }}
                                            </span>
                                            <br><small class="text-muted fst-italic">Mostrador / Ocasional</small>
                                        @else
                                            <span class="fw-bold text-dark">
                                                <i class="bi bi-person-check text-success me-1"></i> {{ $turno->cliente->nombre ?? $turno->cliente->name ?? '-' }}
                                            </span>
                                            @if(!empty($turno->cliente->telefono))
                                                <br><small class="text-muted">{{ $turno->cliente->telefono }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if(optional($turno->pago)->payment_id_mp)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                                <i class="bi bi-shield-check me-1"></i> Online (MP)
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill">
                                                <i class="bi bi-shop me-1"></i> Mostrador (Desktop)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-secondary fw-semibold">
                                            @if($turno->servicios->isNotEmpty())
                                                {{ $turno->servicios->pluck('nombre')->implode(', ') }}
                                            @else
                                                {{ $turno->servicio->nombre ?? '-' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $turno->sucursal->nombre ?? '-' }}</td>
                                    <td>
                                        @php
                                            if ($esVencido && !$dentroDeVentana) {
                                                $badgeEstado = 'bg-secondary text-white';
                                                $textoEstado = 'Vencido';
                                            } else {
                                                $badgeEstado = match($turno->estado) {
                                                    'completado' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'cancelado'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                    'ausente'    => 'bg-dark text-white',
                                                    'confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold',
                                                    default      => 'bg-light text-dark border'
                                                };
                                                $textoEstado = $turno->estado === 'ausente' ? 'Ausente (No-Show)' : ucfirst($turno->estado);
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeEstado }} px-3 py-1 rounded-pill">{{ $textoEstado }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($puedeOperar)
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                <form action="{{ route('admin.turnos.completar', $turno->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmás que el servicio fue completado con éxito?');">
                                                    @csrf 
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-success fw-bold text-white shadow-sm px-3 py-1 rounded-pill" title="Marcar como completado">
                                                        <i class="bi bi-check-circle me-1"></i> Completado
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.turnos.ausente', $turno->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Confirmás que el cliente no se presentó? Esto sumará una falta web y lista negra si llega a 3.');">
                                                    @csrf 
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-dark fw-bold shadow-sm px-3 py-1 rounded-pill" title="Registrar inasistencia">
                                                        <i class="bi bi-x-circle me-1"></i> Ausente
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-muted small fst-italic">Fuera de ventana operativa</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ======================================================= -->
                <!-- 📱 VISTA MOBILE (Tarjetas Táctiles Modernas) -->
                <!-- ======================================================= -->
                <div class="d-block d-lg-none">
                    <div class="row g-3">
                        @foreach($turnosData as $turno)
                        @php
                            $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora);
                            $duracionMinutos = $turno->servicios->isNotEmpty() 
                                ? $turno->servicios->sum('duracion') 
                                : ($turno->servicio->duracion ?? 30);
                            
                            $finTurno = (clone $fechaHoraTurno)->addMinutes($duracionMinutos);
                            $esTerminado = in_array($turno->estado, ['completado', 'cancelado', 'ausente']);
                            $esVencido = $ahora->gt($finTurno) && !$esTerminado;
                            $dentroDeVentana = $ahora->gte($fechaHoraTurno) && $ahora->lte((clone $finTurno)->addHours(3));
                            $puedeOperar = in_array($turno->estado, ['pendiente', 'confirmado']) && $dentroDeVentana;
                        @endphp
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white position-relative">
                                
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="text-dark fw-bold fs-6">
                                            <i class="bi bi-calendar-event text-muted me-1"></i> {{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}
                                        </span>
                                        <div class="text-dark fw-bold font-monospace mt-1">
                                            <i class="bi bi-clock me-1 text-muted"></i> {{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs
                                        </div>
                                    </div>
                                    <div>
                                        @php
                                            if ($esVencido && !$dentroDeVentana) {
                                                $badgeEstado = 'bg-secondary text-white';
                                                $textoEstado = 'Vencido';
                                            } else {
                                                $badgeEstado = match($turno->estado) {
                                                    'completado' => 'bg-success-subtle text-success border border-success-subtle',
                                                    'cancelado'  => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                    'ausente'    => 'bg-dark text-white',
                                                    'confirmado' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold',
                                                    default      => 'bg-light text-dark border'
                                                };
                                                $textoEstado = $turno->estado === 'ausente' ? 'Ausente (No-Show)' : ucfirst($turno->estado);
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeEstado }} px-2 py-1 rounded-pill">{{ $textoEstado }}</span>
                                    </div>
                                </div>

                                <hr class="text-muted opacity-10 my-2">

                                <div class="mb-3 small">
                                    <div class="mb-1">
                                        <span class="text-muted">Cliente:</span>
                                        <strong class="text-dark">
                                            @if(!empty($turno->cliente_nombre_manual))
                                                {{ $turno->cliente_nombre_manual }} <span class="text-muted fw-normal">(Mostrador)</span>
                                            @else
                                                {{ $turno->cliente->nombre ?? $turno->cliente->name ?? '-' }}
                                            @endif
                                        </strong>
                                    </div>
                                    <div class="mb-1">
                                        <span class="text-muted">Servicio:</span>
                                        <span class="fw-semibold text-secondary">
                                            @if($turno->servicios->isNotEmpty())
                                                {{ $turno->servicios->pluck('nombre')->implode(', ') }}
                                            @else
                                                {{ $turno->servicio->nombre ?? '-' }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted small">Sucursal: {{ $turno->sucursal->nombre ?? '-' }}</span>
                                        <div>
                                            @if(optional($turno->pago)->payment_id_mp)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 rounded-pill" style="font-size: 11px;">Online (MP)</span>
                                            @else
                                                <span class="badge bg-light text-dark border px-2 py-0 rounded-pill" style="font-size: 11px;">Mostrador</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-light p-2 rounded-3 text-center">
                                    @if($puedeOperar)
                                        <div class="d-flex gap-2 justify-content-center">
                                            <form action="{{ route('admin.turnos.completar', $turno->id) }}" method="POST" class="w-50" onsubmit="return confirm('¿Confirmás que el servicio fue completado?');">
                                                @csrf 
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success fw-bold text-white shadow-sm w-100 rounded-pill py-1">
                                                    <i class="bi bi-check-circle me-1"></i> Completar
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.turnos.ausente', $turno->id) }}" method="POST" class="w-50" onsubmit="return confirm('¿Confirmás ausencia? Sumará penalización si es web.');">
                                                @csrf 
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-dark fw-bold shadow-sm w-100 rounded-pill py-1">
                                                    <i class="bi bi-x-circle me-1"></i> Ausente
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small fst-italic d-block py-1">Fuera de ventana operativa</span>
                                    @endif
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        @endif
    @endforeach
@else
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
        <div class="py-4">
            <i class="bi bi-calendar-x display-5 text-muted opacity-50 d-block mb-3"></i>
            <h5 class="fw-bold text-dark mb-1">Sin turnos encontrados</h5>
            <p class="text-muted small mb-0">No hay registros que coincidan con los filtros de búsqueda seleccionados.</p>
        </div>
    </div>
@endif