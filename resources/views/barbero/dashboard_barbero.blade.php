@extends('layouts.barbero')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4 barbero-dashboard">

    <!-- 🌟 Hero Section con Mini Métricas Operativas -->
    <div class="hero-barber-card p-4 rounded-4 mb-4 text-white shadow-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-left: 6px solid #168F87;">
        <div class="row align-items-center g-3 position-relative z-1">
            <div class="col-12 col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge px-3 py-1 rounded-pill small fw-bold" style="background: rgba(22, 143, 135, 0.2); color: #2dd4bf; border: 1px solid rgba(45, 212, 191, 0.3);">
                        <i class="bi bi-circle-fill me-1 small animate-pulse"></i> PANEL OPERATIVO DE SILLÓN
                    </span>
                </div>
                <h1 class="fw-bold text-white mb-1" style="font-family: 'Bebas Neue', sans-serif; font-size: 3rem; letter-spacing: 1.5px;">
                    Agenda de <span style="color: #2dd4bf;">{{ auth()->user()->nombre ?? auth()->user()->name }}</span>
                </h1>
                <p class="text-gray-400 small mb-0 opacity-75">Gestión en tiempo real de citas, control de caja y penalización de ausencias.</p>
            </div>
            
            <div class="col-12 col-lg-5 text-lg-end">
                <div class="bg-dark bg-opacity-50 p-3 rounded-4 border border-secondary border-opacity-25 d-inline-flex align-items-center gap-3">
                    <div class="text-start">
                        <span class="d-block text-uppercase fw-bold text-muted" style="font-size: 0.65rem; letter-spacing: 1px;">Reloj Operativo</span>
                        <span class="fs-5 fw-bold font-monospace text-white" id="liveClock">{{ now()->format('d/m/Y - H:i:s') }}</span>
                    </div>
                    <div class="p-2 rounded-3 text-white" style="background-color: #168F87;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 💡 Guía Operativa en formato Accordion Oscuro -->
    <div class="mb-4">
        <div class="accordion border-0 shadow-sm rounded-4 overflow-hidden" id="accordionGuia">
            <div class="accordion-item border-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed bg-white text-dark fw-bold px-4 py-3 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuia">
                        <i class="bi bi-shield-lock-fill me-2" style="color: #168F87;"></i>
                        Guía Operativa & Reglas del Sistema
                    </button>
                </h2>
                <div id="collapseGuia" class="accordion-collapse collapse" data-bs-parent="#accordionGuia">
                    <div class="accordion-body bg-light p-4">
                        <div class="row g-3 small">
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-white rounded-3 shadow-sm border-start border-3 border-warning">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-clock me-1"></i> Ventana Operativa (3 hs)</strong>
                                    <span class="text-muted">Las acciones de <em>Completar</em> o <em>Ausente</em> se activan desde la hora del turno hasta 3hs después.</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-white rounded-3 shadow-sm border-start border-3 border-success">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-shop me-1"></i> Mostrador / Desktop</strong>
                                    <span class="text-muted">No poseen `payment_id_mp`. Al marcarlos completados impactan directo en la caja.</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-white rounded-3 shadow-sm border-start border-3 border-danger">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-exclamation-octagon me-1"></i> Turnos Online & No-Show</strong>
                                    <span class="text-muted">Tienen `payment_id_mp`. Marcar ausente incrementa las faltas web del cliente (baneo a la 3ª falta).</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔍 Toolbar de Filtros con Estilo Oscuro / Moderno -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-dark text-white px-4 py-3 d-flex align-items-center justify-content-between">
            <span class="fw-bold small text-uppercase" style="letter-spacing: 1px;"><i class="bi bi-funnel-fill text-warning me-1"></i> Filtros de Búsqueda</span>
            <span class="badge bg-secondary bg-opacity-50 text-white rounded-pill px-3 py-1 small">Búsqueda rápida</span>
        </div>
        <div class="card-body p-4">
            <form class="row g-3 align-items-end" id="formFiltrosTurnos" onsubmit="return false;">
                <div class="col-12 col-md-4">
                    <label class="form-label text-dark fw-bold small">Cliente / Alias</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="filtroCliente" class="form-control border-start-0 bg-light" placeholder="Ej: Juan Pérez..." maxlength="50">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label text-dark fw-bold small">Fecha Específica</label>
                    <input type="date" id="filtroFecha" class="form-control bg-light shadow-sm rounded-3">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label text-dark fw-bold small">Estado del Turno</label>
                    <select id="filtroEstado" class="form-select bg-light shadow-sm rounded-3">
                        <option value="">Todos los estados</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="confirmado">Confirmados</option>
                        <option value="completado">Completados</option>
                        <option value="cancelado">Cancelados</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" id="btnReset" class="btn btn-dark w-100 fw-bold rounded-3 shadow-sm py-2">
                        <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📋 Contenedor AJAX del Listado -->
    <div id="contenedorTablaTurnos">
        @include('barbero.partials.tabla_turnos', [
            'turnosHoy' => $turnosHoy,
            'turnosFuturos' => $turnosFuturos,
            'turnosFinalizados' => $turnosFinalizados
        ])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    setInterval(function() {
        const now = new Date();
        const dateStr = now.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
        const el = document.getElementById('liveClock');
        if (el) el.textContent = `${dateStr} - ${timeStr}`;
    }, 1000);

    const inputCliente = document.getElementById('filtroCliente');
    const inputFecha = document.getElementById('filtroFecha');
    const inputEstado = document.getElementById('filtroEstado');
    const btnReset = document.getElementById('btnReset');
    const contenedor = document.getElementById('contenedorTablaTurnos');

    let debounceTimer = null;

    inputCliente.addEventListener('input', function() {
        this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(buscar, 300);
    });

    inputFecha.addEventListener('change', buscar);
    inputEstado.addEventListener('change', buscar);

    function buscar() {
        const queryParams = new URLSearchParams({
            cliente: inputCliente.value.trim(),
            fecha: inputFecha.value,
            estado: inputEstado.value
        });

        contenedor.style.opacity = '0.5';

        fetch(`{{ route('barbero.turnos') }}?${queryParams.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            contenedor.innerHTML = html;
            contenedor.style.opacity = '1';
        })
        .catch(err => {
            console.error(err);
            contenedor.style.opacity = '1';
        });
    }

    btnReset.addEventListener('click', function() {
        inputCliente.value = '';
        inputFecha.value = '';
        inputEstado.value = '';
        buscar();
    });
});
</script>
@endsection