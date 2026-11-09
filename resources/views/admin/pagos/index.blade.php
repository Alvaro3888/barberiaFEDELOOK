@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 py-md-5 px-lg-4" style="background-color: #f4f7f6; min-height: 100vh;">

    <!-- ENCABEZADO ESTILO SUCURSALES -->
    <div class="p-4 p-lg-5 rounded-4 text-white mb-4 position-relative overflow-hidden shadow-sm animate-fade-in" style="background: linear-gradient(135deg, #111111 0%, #1f2937 100%);">
        <div class="position-relative z-2">
            <span class="badge px-3 py-2 rounded-pill text-uppercase mb-2 shadow-sm fw-bold btn-no-select" style="background: #00a99d; color: #fff; letter-spacing: 1.5px; font-size: 0.7rem;">
                Módulo Financiero
            </span>
            <h1 class="display-6 fw-bold mb-1 text-white fs-4 fs-lg-3" style="letter-spacing: -0.01em;">
                Movimientos de Pagos
            </h1>
            <p class="text-white-50 mb-0 small" style="max-width: 600px; font-size: 0.95rem;">
                Monitoreá las transacciones, filtrá por cliente o servicio y verificá los identificadores de pagos de Mercado Pago en tiempo real.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-fade-in" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-fade-in" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TARJETA DE FILTROS EN VIVO -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white card-modern">
        <div class="card-body p-3 p-lg-4">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label for="filter-cliente" class="form-label small fw-bold text-dark mb-1">Cliente</label>
                    <input type="text" id="filter-cliente" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="Buscar por cliente..." style="font-size: 0.85rem;" autocomplete="off">
                </div>

                <div class="col-12 col-md-3">
                    <label for="filter-servicio" class="form-label small fw-bold text-dark mb-1">Servicio</label>
                    <select id="filter-servicio" class="form-select rounded-3 border bg-light py-2 px-3 shadow-none" style="font-size: 0.85rem;">
                        <option value="">Todos los servicios</option>
                        @foreach($servicios as $servicio)
                            <option value="{{ strtolower($servicio->nombre) }}">
                                {{ $servicio->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="filter-payment" class="form-label small fw-bold text-dark mb-1">Payment ID (Mercado Pago)</label>
                    <input type="text" id="filter-payment" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="Buscar Payment ID..." style="font-size: 0.85rem;" autocomplete="off">
                </div>

                <div class="col-12 col-md-3">
                    <button type="button" id="btn-limpiar" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-bold w-100 btn-no-select btn-with-spinner">
                        <span class="btn-text">Limpiar Filtros</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR HÍBRIDO DE PAGOS -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white card-modern">
        <div class="card-body p-0">
            
            <!-- ================= VISTA DESKTOP (TABLA CON SCROLL INTERNO Y ENCABEZADO STICKY) ================= -->
            <div class="table-responsive d-none d-md-block" style="max-height: 480px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" id="tabla-pagos">
                    <thead class="bg-light text-uppercase text-secondary sticky-top shadow-xs" style="font-size: 0.72rem; letter-spacing: 0.08em; z-index: 5; background-color: #f8f9fa !important;">
                        <tr>
                            <th class="py-3 ps-4 bg-light">Fecha</th>
                            <th class="py-3 bg-light">Cliente</th>
                            <th class="py-3 bg-light">Barbero</th>
                            <th class="py-3 bg-light">Sucursal</th>
                            <th class="py-3 bg-light">Servicio</th>
                            <th class="py-3 bg-light">Monto</th>
                            <th class="py-3 bg-light">Método</th>
                            <th class="py-3 bg-light">Estado</th>
                            <th class="py-3 pe-4 bg-light">Payment ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr class="fila-pago" 
                                data-cliente="{{ strtolower($pago->turno?->cliente?->nombre ?? '') }}"
                                data-servicio="{{ strtolower($pago->turno?->servicio?->nombre ?? '') }}"
                                data-payment="{{ strtolower($pago->payment_id_mp ?? '') }}"
                                style="transition: background-color 0.2s ease;">
                                
                                <td class="ps-4 py-3 small text-secondary">
                                    {{ $pago->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="py-3 fw-bold text-dark">
                                    {{ $pago->turno?->cliente?->nombre ?? 'N/D' }}
                                </td>

                                <td class="py-3 small text-secondary">
                                    <i class="bi bi-person-fill me-1 text-teal"></i>{{ $pago->turno?->barbero?->nombre ?? 'N/D' }}
                                </td>

                                <td class="py-3 small text-secondary">
                                    {{ $pago->turno?->sucursal?->nombre ?? 'N/D' }}
                                </td>

                                <td class="py-3 fw-semibold text-dark">
                                    {{ $pago->turno?->servicio?->nombre ?? 'N/D' }}
                                </td>

                                <td class="py-3 fw-bold text-dark">
                                    ${{ number_format($pago->monto, 0, ',', '.') }}
                                </td>

                                <td class="py-3 small">
                                    {{ ucfirst($pago->metodo) }}
                                </td>

                                <td class="py-3">
                                    @if($pago->estado === 'pagado')
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold btn-no-select" style="background: rgba(0, 169, 157, 0.12); color: #00a99d; font-size: 0.75rem;">
                                            Pagado
                                        </span>
                                    @elseif($pago->estado === 'pendiente')
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold btn-no-select" style="background: rgba(255, 193, 7, 0.15); color: #856404; font-size: 0.75rem;">
                                            Pendiente
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold btn-no-select" style="background: rgba(220, 53, 69, 0.12); color: #dc3545; font-size: 0.75rem;">
                                            {{ ucfirst($pago->estado) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="pe-4 py-3 small text-muted font-monospace">
                                    {{ $pago->payment_id_mp ?? 'N/D' }}
                                </td>
                            </tr>
                        @empty
                            <tr id="sin-registros-inicial">
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <span class="fs-2 d-block mb-2">💳</span>
                                        <p class="mb-0 fw-semibold">No existen pagos registrados.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <tr id="sin-coincidencias" class="d-none">
                            <td colspan="9" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <span class="fs-2 d-block mb-2">🔍</span>
                                    <p class="mb-0 fw-semibold">No se encontraron pagos que coincidan con la búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ================= VISTA MÓVIL (TARJETAS TÁCTILES) ================= -->
            <div class="d-block d-md-none p-3" style="max-height: 500px; overflow-y: auto;">
                <div class="row g-3" id="lista-pagos-movil">
                    @forelse($pagos as $pago)
                        <div class="col-12 fila-pago-movil"
                            data-cliente="{{ strtolower($pago->turno?->cliente?->nombre ?? '') }}"
                            data-servicio="{{ strtolower($pago->turno?->servicio?->nombre ?? '') }}"
                            data-payment="{{ strtolower($pago->payment_id_mp ?? '') }}">
                            
                            <div class="card border-0 shadow-xs rounded-4 p-3 bg-white" style="border: 1px solid #e2e8f0 !important;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                            {{ $pago->turno?->cliente?->nombre ?? 'Cliente Anónimo' }}
                                        </h6>
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>{{ $pago->created_at?->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <span class="badge fw-bold px-3 py-1.5 rounded-pill btn-no-select" style="font-size: 0.8rem; background: rgba(0, 169, 157, 0.12); color: #00a99d;">
                                        ${{ number_format($pago->monto, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="mb-2 small text-secondary border-top pt-2" style="font-size: 0.85rem;">
                                    <div class="fw-semibold text-dark mb-1">Servicio: {{ $pago->turno?->servicio?->nombre ?? 'N/D' }}</div>
                                    <div><i class="bi bi-person-fill me-1 text-teal"></i>Barbero: {{ $pago->turno?->barbero?->nombre ?? 'N/D' }}</div>
                                    <div><i class="bi bi-geo-alt-fill me-1 text-teal"></i>Sucursal: {{ $pago->turno?->sucursal?->nombre ?? 'N/D' }}</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-1">
                                    <div>
                                        @if($pago->estado === 'pagado')
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold btn-no-select" style="background: rgba(0, 169, 157, 0.12); color: #00a99d; font-size: 0.7rem;">Pagado</span>
                                        @elseif($pago->estado === 'pendiente')
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold btn-no-select" style="background: rgba(255, 193, 7, 0.15); color: #856404; font-size: 0.7rem;">Pendiente</span>
                                        @else
                                            <span class="badge rounded-pill px-2.5 py-1 fw-bold btn-no-select" style="background: rgba(220, 53, 69, 0.12); color: #dc3545; font-size: 0.7rem;">{{ ucfirst($pago->estado) }}</span>
                                        @endif
                                        <span class="text-muted small ms-2" style="font-size: 0.75rem;">({{ ucfirst($pago->metodo) }})</span>
                                    </div>
                                    <span class="text-muted font-monospace small" style="font-size: 0.75rem;">ID: {{ $pago->payment_id_mp ?? 'N/D' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted" id="sin-registros-movil">
                            <span class="fs-2 d-block mb-2">💳</span>
                            <p class="mb-0 small fw-semibold">No existen pagos registrados.</p>
                        </div>
                    @endforelse

                    <div class="col-12 text-center py-4 text-muted d-none" id="sin-coincidencias-movil">
                        <span class="fs-2 d-block mb-2">🔍</span>
                        <p class="mb-0 small fw-semibold">No se encontraron pagos que coincidan con la búsqueda.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- ESTILOS Y ANIMACIONES -->
<style>
.text-teal { color: #00a99d !important; }
.transition-all { transition: all 0.2s ease; }
.transition-all:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.btn-no-select {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
.card-modern {
    box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
    border: none;
}
/* Spinner de Tijeras Animadas */
.scissors-spin {
    display: inline-block;
    animation: scissorsSpin 0.9s linear infinite;
}
@keyframes scissorsSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCliente = document.getElementById('filter-cliente');
    const selectServicio = document.getElementById('filter-servicio');
    const inputPayment = document.getElementById('filter-payment');
    const btnLimpiar = document.getElementById('btn-limpiar');
    
    const filasDesktop = document.querySelectorAll('.fila-pago');
    const filaSinCoincidenciasDesktop = document.getElementById('sin-coincidencias');

    const filasMovil = document.querySelectorAll('.fila-pago-movil');
    const filaSinCoincidenciasMovil = document.getElementById('sin-coincidencias-movil');

    // Prevenir selección rápida en botones
    document.querySelectorAll('.btn-no-select').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            if (e.detail > 1) e.preventDefault();
        });
    });

    function filtrarTabla() {
        const valCliente = inputCliente.value.toLowerCase().trim();
        const valServicio = selectServicio.value.toLowerCase().trim();
        const valPayment = inputPayment.value.toLowerCase().trim();

        let coincidenciasDesktop = 0;
        let coincidenciasMovil = 0;

        // Filtrar escritorio
        filasDesktop.forEach(function(fila) {
            const cliente = fila.getAttribute('data-cliente');
            const servicio = fila.getAttribute('data-servicio');
            const payment = fila.getAttribute('data-payment');

            if (cliente.includes(valCliente) && (valServicio === '' || servicio === valServicio) && payment.includes(valPayment)) {
                fila.classList.remove('d-none');
                coincidenciasDesktop++;
            } else {
                fila.classList.add('d-none');
            }
        });

        if (filasDesktop.length > 0 && filaSinCoincidenciasDesktop) {
            if (coincidenciasDesktop === 0) {
                filaSinCoincidenciasDesktop.classList.remove('d-none');
            } else {
                filaSinCoincidenciasDesktop.classList.add('d-none');
            }
        }

        // Filtrar móvil
        filasMovil.forEach(function(fila) {
            const cliente = fila.getAttribute('data-cliente');
            const servicio = fila.getAttribute('data-servicio');
            const payment = fila.getAttribute('data-payment');

            if (cliente.includes(valCliente) && (valServicio === '' || servicio === valServicio) && payment.includes(valPayment)) {
                fila.classList.remove('d-none');
                coincidenciasMovil++;
            } else {
                fila.classList.add('d-none');
            }
        });

        if (filasMovil.length > 0 && filaSinCoincidenciasMovil) {
            if (coincidenciasMovil === 0) {
                filaSinCoincidenciasMovil.classList.remove('d-none');
            } else {
                filaSinCoincidenciasMovil.classList.add('d-none');
            }
        }
    }

    inputCliente.addEventListener('input', filtrarTabla);
    selectServicio.addEventListener('change', filtrarTabla);
    inputPayment.addEventListener('input', filtrarTabla);

    // SISTEMA DE LIMPIEZA CON SPINNER DE TIJERAS CONDICIONAL (Retraso de 250ms)
    btnLimpiar.addEventListener('click', function() {
        if (this.disabled) return;
        this.disabled = true;

        const textSpan = this.querySelector('.btn-text');
        const originalText = textSpan ? textSpan.innerHTML : this.innerHTML;

        let spinnerTimeout = setTimeout(() => {
            if (textSpan) {
                textSpan.innerHTML = `<i class="bi bi-scissors scissors-spin me-1"></i> Limpiando...`;
            }
        }, 250);

        inputCliente.value = '';
        selectServicio.value = '';
        inputPayment.value = '';
        filtrarTabla();

        setTimeout(() => {
            clearTimeout(spinnerTimeout);
            this.disabled = false;
            if (textSpan) {
                textSpan.innerHTML = originalText;
            }
        }, 400);
    });
});
</script>
@endsection