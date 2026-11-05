@extends('layouts.public')

@section('content')
<div class="container my-5 pt-4">

    <!-- Cabecera con título y botón de reserva -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="font-family: 'Bebas Neue', sans-serif; font-size: 2.8rem; letter-spacing: 1px; color: #111;">Mis Turnos</h2>
            <p class="text-muted mb-0">Gestioná tus citas de forma rápida o reservá un nuevo espacio con tu barbero favorito.</p>
        </div>
        <a href="{{ route('usuario.catalogo') }}" class="btn btn-dark fw-bold px-4 py-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 transition-all hover-scale">
            <i class="bi bi-plus-circle fs-5 text-warning"></i> Reservar nuevo turno
        </a>
    </div>

    {{-- 🚨 Alerta de Penalización / Lista Negra (Si aplica) --}}
    @if(auth()->user() && auth()->user()->estaPenalizado())
        <div class="alert border-0 shadow-sm rounded-4 mb-4 d-flex align-items-start gap-3 p-4 text-white" style="background: linear-gradient(135deg, #b02a37, #842029);" role="alert">
            <i class="bi bi-shield-fill-exclamation fs-3 mt-1 text-warning"></i>
            <div>
                <h5 class="fw-bold mb-1">¡Cuenta Suspendida Temporalmente (Lista Negra)!</h5>
                <p class="mb-0 small opacity-95">
                    Debido a reiteradas ausencias o cancelaciones a última hora en tus turnos online, tu cuenta tiene bloqueada la creación de nuevos turnos web hasta el <strong>{{ \Carbon\Carbon::parse(auth()->user()->penalizado_hasta)->format('d/m/Y H:i') }} hs</strong>. 
                    Si necesitás asistencia urgente, comunicate directamente con el local.
                </p>
            </div>
        </div>
    @endif

    {{-- ℹ️ Alerta informativa sobre las políticas de cancelación (Estilo moderno oscuro/gris) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light p-3 p-md-4">
        <div class="d-flex align-items-start gap-3">
            <div class="bg-dark text-warning p-2 rounded-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-clock-history fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Políticas de Cancelación y Reprogramación:</h6>
                <ul class="mb-0 small text-secondary ps-3">
                    <li><strong>Más de 24 horas antes:</strong> Podés cancelar con <strong>reembolso automático</strong> por Mercado Pago o reprogramar sin cargo.</li>
                    <li><strong>Nota importante:</strong> Los turnos que hayan sido reprogramados previamente pierden el derecho al reembolso automático ante una cancelación posterior.</li>
                    <li><strong>Entre 24 y 2 horas antes:</strong> Podés cancelar (sin derecho a reembolso) o reprogramar (hasta un máximo de 2 veces).</li>
                    <li><strong>Menos de 2 horas antes:</strong> Las opciones de autogestión se bloquean por completo (candado). Deberás comunicarte con la sucursal.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Mensajes de éxito y error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 bg-success text-white" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 bg-danger text-white" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Contenedor principal de la tabla (Versión 2.0 rediseñada sin celeste) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-white" style="background-color: #111;">
                    <tr>
                        <th class="py-3 px-4">Fecha y Hora</th>
                        <th class="py-3">Servicio</th>
                        <th class="py-3">Barbero</th>
                        <th class="py-3">Sucursal</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-center">Reprogramaciones</th>
                        <th class="py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($turnos as $turno)
                        @php
                            $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha . ' ' . $turno->hora);
                            $yaPaso = $fechaHoraTurno->isPast();
                            $minutosRestantes = now()->diffInMinutes($fechaHoraTurno, false);
                            
                            $reproCount = $turno->reprogramaciones_count ?? 0;
                            
                            // Reglas lógicas estrictas
                            $puedeReprogramar = !$yaPaso 
                                                    && $turno->estado === 'confirmado' 
                                                    && $minutosRestantes >= 120 
                                                    && $reproCount < 2;

                            $puedeCancelar = !$yaPaso && $turno->estado === 'confirmado' && $minutosRestantes >= 120;
                            
                            // REGLA DE HIERRO (Opción A): Solo hay reembolso si falta >= 24 hs Y NUNCA fue reprogramado
                            $aplicaReembolso = ($minutosRestantes >= 1440) && ($reproCount === 0);
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-bold text-dark d-block">{{ \Carbon\Carbon::parse($turno->fecha)->format('d/m/Y') }}</span>
                                <span class="badge bg-light text-dark border mt-1 font-monospace">{{ \Carbon\Carbon::parse($turno->hora)->format('H:i') }} hs</span>
                            </td>

                            <td class="py-3">
                                <span class="fw-semibold text-secondary">{{ $turno->servicio->nombre ?? 'Servicio no disponible' }}</span>
                            </td>

                            <td class="py-3 text-dark">
                                {{ $turno->barbero->nombre ?? 'Sin barbero' }}
                            </td>

                            <td class="py-3 text-muted small">
                                {{ $turno->sucursal->nombre ?? 'Principal' }}
                            </td>

                            <td class="py-3">
                                @if($turno->estado === 'cancelado')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill">Cancelado</span>
                                @elseif($turno->estado === 'completado')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">Completado</span>
                                @elseif($turno->estado === 'ausente')
                                    <span class="badge bg-dark text-white px-3 py-1 rounded-pill">Ausente / No-Show</span>
                                @elseif($yaPaso)
                                    <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill">Vencido</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold">Confirmado</span>
                                @endif
                            </td>

                            <td class="text-center py-3">
                                <span class="badge {{ $reproCount > 0 ? 'bg-dark text-white' : 'bg-light text-muted border' }} px-2 py-1">
                                    {{ $reproCount }} / 2
                                </span>
                            </td>

                            <td class="text-center py-3">
                                @if(!$yaPaso && $turno->estado === 'confirmado')
                                    <div class="d-flex justify-content-center gap-2">
                                        
                                        {{-- Botón de Reprogramar --}}
                                        @if($puedeReprogramar)
                                            <a href="{{ route('usuario.turnos.edit', $turno->id) }}" class="btn btn-sm btn-outline-dark fw-bold px-3 rounded-pill transition-all">
                                                <i class="bi bi-pencil-square"></i> Reprogramar
                                            </a>
                                        @else
                                            <button type="button" 
                                                    class="btn btn-sm btn-light text-muted border px-3 rounded-pill" 
                                                    disabled 
                                                    title="@if($reproCount >= 2) Límite máximo de 2 reprogramaciones alcanzado. @elseif($minutosRestantes < 120) Bloqueado por cercanía al horario (< 2 horas). @else Opción no disponible. @endif">
                                                <i class="bi bi-lock-fill text-danger"></i> Bloqueado
                                            </button>
                                        @endif

                                        {{-- Botón de Cancelar --}}
                                        @if($puedeCancelar)
                                            <form action="{{ route('usuario.turnos.cancelar', $turno->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger px-3 rounded-pill btn-cancelar-turno"
                                                        data-reembolso="{{ $aplicaReembolso ? '1' : '0' }}">
                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                </button>
                                            </form>
                                        @else
                                            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="Cancelación bloqueada a menos de 2 horas. Contactá al local.">
                                                <button class="btn btn-sm btn-light text-muted border px-3 rounded-pill" disabled style="pointer-events: none;">
                                                    <i class="bi bi-lock-fill text-danger"></i> Candado
                                                </button>
                                            </span>
                                        @endif

                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Sin acciones</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="my-3">
                                    <i class="bi bi-calendar-x display-4 d-block mb-3 text-black-50"></i>
                                    <h5 class="fw-bold text-dark">No tenés turnos activos</h5>
                                    <p class="small text-muted mb-4">Empezá a reservar tus espacios de forma rápida y cómoda.</p>
                                    <a href="{{ route('usuario.catalogo') }}" class="btn btn-dark px-4 py-2 rounded-pill fw-bold">
                                        Reservar tu primer turno ahora
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap si están activos
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    const botonesCancelar = document.querySelectorAll('.btn-cancelar-turno');
    
    botonesCancelar.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            const aplicaReembolso = this.dataset.reembolso === '1';
            let mensaje = '¿Estás seguro de que deseas cancelar este turno?';

            if (aplicaReembolso) {
                mensaje += '\n\n✅ Al faltar más de 24 hs y no haber sido reprogramado, se procesará el reembolso automático de tu dinero a través de Mercado Pago.';
            } else {
                mensaje += '\n\n⚠️ ATENCIÓN: Este turno ya fue reprogramado previamente o se cancela con menos de 24 hs de anticipación, por lo que NO aplicará reembolso del importe abonado.';
            }

            if (!confirm(mensaje)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection