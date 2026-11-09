@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 py-md-5 px-lg-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- ENCABEZADO ESTILO SUCURSALES -->
    <div class="p-4 p-lg-5 rounded-4 text-white mb-4 position-relative overflow-hidden shadow-sm animate-fade-in" style="background: linear-gradient(135deg, #111111 0%, #1f2937 100%);">
        <div class="position-relative z-2">
            <span class="badge px-3 py-2 rounded-pill text-uppercase mb-2 shadow-sm fw-bold btn-no-select" style="background: #00a99d; color: #fff; letter-spacing: 1.5px; font-size: 0.7rem;">
                Módulo de Moderación
            </span>
            <h1 class="display-6 fw-bold mb-1 text-white fs-4 fs-lg-3" style="letter-spacing: -0.01em;">
                Calificaciones y Reseñas
            </h1>
            <p class="text-white-50 mb-0 small" style="max-width: 600px; font-size: 0.95rem;">
                Administrá y filtrá las opiniones recibidas. Mantené la calidad de la plataforma dando de baja reseñas malintencionadas de forma instantánea.
            </p>
        </div>
    </div>

    <!-- TARJETA DE FILTROS MEJORADA -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white card-modern">
        <div class="card-body p-3 p-lg-4">
            <form id="filter-form">
                <input type="hidden" name="estrellas" id="filter-estrellas" value="{{ request('estrellas') }}">

                <div class="row g-3 align-items-end">
                    
                    <!-- Filtro por Estrellas -->
                    <div class="col-12 col-xl-7">
                        <label class="form-label small fw-bold text-dark mb-2">Filtrar por Estrellas</label>
                        <div class="d-flex flex-wrap gap-2" id="stars-filter-container">
                            <button type="button" data-value="" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">Todas</span>
                            </button>
                            <button type="button" data-value="5" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '5' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">⭐ 5 Estrellas</span>
                            </button>
                            <button type="button" data-value="4" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '4' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">⭐ 4 Estrellas</span>
                            </button>
                            <button type="button" data-value="3" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '3' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">⭐ 3 Estrellas</span>
                            </button>
                            <button type="button" data-value="2" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '2' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">⭐ 2 Estrellas</span>
                            </button>
                            <button type="button" data-value="1" class="btn btn-sm rounded-pill px-3 py-1.5 fw-bold filter-star-btn btn-no-select {{ request('estrellas') == '1' ? 'active-star' : 'btn-light text-secondary border' }}" style="font-size: 0.78rem;">
                                <span class="btn-text">⭐ 1 Estrella</span>
                            </button>
                        </div>
                    </div>

                    <!-- Estado de Visibilidad -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label small fw-bold text-dark mb-1">Estado de Visibilidad</label>
                        <select name="estado" id="filter-estado" class="form-select rounded-3 border bg-light py-2 px-3 shadow-none" style="font-size: 0.85rem;">
                            <option value="">Todos los estados</option>
                            <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>Visibles en la web (Activos)</option>
                            <option value="ocultos" {{ request('estado') == 'ocultos' ? 'selected' : '' }}>Dados de baja (Ocultos)</option>
                        </select>
                    </div>

                    <!-- Botón Limpiar -->
                    <div class="col-12 col-md-6 col-xl-2">
                        <button type="button" id="btn-limpiar" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-bold w-100 btn-no-select">
                            <span class="btn-text">Limpiar</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- CONTENEDOR DE LA TABLA Y TARJETAS MÓVILES -->
    <div id="table-container" class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white position-relative card-modern">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3 mx-lg-4 mt-4 rounded-4 shadow-sm border-0 animate-fade-in" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- VISTA DESKTOP (TABLA CON SCROLL INTERNO Y STICKY HEADER) -->
        <div class="table-responsive d-none d-md-block" style="max-height: 480px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase text-secondary sticky-top shadow-xs" style="font-size: 0.72rem; letter-spacing: 0.08em; z-index: 5; background-color: #f8f9fa !important;">
                    <tr>
                        <th class="py-3 ps-4 bg-light">Cliente</th>
                        <th class="py-3 bg-light">Puntuación</th>
                        <th class="py-3 bg-light">Comentario</th>
                        <th class="py-3 bg-light">Servicio / Barbero</th>
                        <th class="py-3 bg-light">Estado</th>
                        <th class="py-3 text-end pe-4 bg-light">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calificaciones as $cal)
                        <tr class="{{ !$cal->activo ? 'bg-light text-muted' : '' }}" style="transition: background-color 0.2s ease;">
                            <td class="ps-4 py-3 fw-bold text-dark">
                                {{ $cal->cliente->nombre ?? $cal->cliente->name ?? 'Cliente Anónimo' }}
                            </td>
                            <td class="py-3">
                                <span class="badge fw-bold px-3 py-2 rounded-pill shadow-sm btn-no-select" style="background: rgba(255, 193, 7, 0.15); color: #856404; font-size: 0.8rem;">
                                    ⭐ {{ $cal->puntuacion }} / 5
                                </span>
                            </td>
                            <td class="py-3" style="max-width: 320px;">
                                <p class="mb-0 small {{ !$cal->activo ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="word-break: break-word; line-height: 1.5;">
                                    "{{ $cal->comentario ?: 'Sin comentario registrado' }}"
                                </p>
                            </td>
                            <td class="py-3 small text-secondary">
                                <div class="fw-semibold text-dark">{{ $cal->turno->servicio->nombre ?? 'N/D' }}</div>
                                <div class="text-muted" style="font-size: 0.78rem;"><i class="bi bi-person-fill me-1 text-teal"></i>{{ $cal->turno->barbero->nombre ?? 'N/D' }}</div>
                            </td>
                            <td class="py-3">
                                @if($cal->activo)
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold btn-no-select" style="background: rgba(0, 169, 157, 0.12); color: #00a99d; font-size: 0.75rem;">
                                        Visible
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold btn-no-select" style="background: rgba(220, 53, 69, 0.12); color: #dc3545; font-size: 0.75rem;">
                                        Dado de baja
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <form action="{{ route('admin.calificaciones.toggle', $cal->id) }}" method="POST" class="d-inline form-action-prevent">
                                    @csrf
                                    @method('PATCH')
                                    @if($cal->activo)
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold transition-all btn-with-spinner btn-no-select" style="background: #fff; border: 1px solid #dc3545; color: #dc3545; font-size: 0.78rem;" onclick="return confirm('¿Seguro que querés dar de baja este comentario? Ya no se mostrará en la web pública.')">
                                            <span class="btn-text">Dar de baja</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold transition-all btn-with-spinner btn-no-select" style="background: #00a99d; border: 1px solid #00a99d; color: #fff; font-size: 0.78rem;">
                                            <span class="btn-text">Reactivar</span>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <span class="fs-2 d-block mb-2">⭐</span>
                                    <p class="mb-0 fw-semibold">No se encontraron calificaciones con los filtros seleccionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- VISTA MÓVIL (TARJETAS TÁCTILES) -->
        <div class="d-block d-md-none p-3" style="max-height: 500px; overflow-y: auto;">
            <div class="row g-3">
                @forelse($calificaciones as $cal)
                    <div class="col-12">
                        <div class="card border-0 shadow-xs rounded-4 p-3 bg-white {{ !$cal->activo ? 'bg-light text-muted' : '' }}" style="border: 1px solid #e2e8f0 !important;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                        {{ $cal->cliente->nombre ?? $cal->cliente->name ?? 'Cliente Anónimo' }}
                                    </h6>
                                    <span class="badge fw-bold px-2.5 py-1 rounded-pill btn-no-select" style="background: rgba(255, 193, 7, 0.15); color: #856404; font-size: 0.75rem;">
                                        ⭐ {{ $cal->puntuacion }} / 5
                                    </span>
                                </div>
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold btn-no-select" style="background: {{ $cal->activo ? 'rgba(0, 169, 157, 0.12); color: #00a99d;' : 'rgba(220, 53, 69, 0.12); color: #dc3545;' }} font-size: 0.7rem;">
                                    {{ $cal->activo ? 'Visible' : 'De baja' }}
                                </span>
                            </div>

                            <p class="mb-2 small {{ !$cal->activo ? 'text-decoration-line-through text-muted' : 'text-dark' }}" style="word-break: break-word; font-style: italic;">
                                "{{ $cal->comentario ?: 'Sin comentario registrado' }}"
                            </p>

                            <div class="mb-3 small text-secondary border-top pt-2" style="font-size: 0.8rem;">
                                <div class="fw-semibold text-dark">Servicio: {{ $cal->turno->servicio->nombre ?? 'N/D' }}</div>
                                <div class="text-muted"><i class="bi bi-person-fill me-1 text-teal"></i>Barbero: {{ $cal->turno->barbero->nombre ?? 'N/D' }}</div>
                            </div>

                            <div class="d-flex justify-content-end border-top pt-2">
                                <form action="{{ route('admin.calificaciones.toggle', $cal->id) }}" method="POST" class="w-100 form-action-prevent">
                                    @csrf
                                    @method('PATCH')
                                    @if($cal->activo)
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100 fw-bold py-1.5 btn-with-spinner btn-no-select" style="font-size: 0.78rem;" onclick="return confirm('¿Seguro que querés dar de baja este comentario?')">
                                            <span class="btn-text">Dar de baja</span>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm text-white rounded-pill w-100 fw-bold py-1.5 btn-with-spinner btn-no-select" style="background: #00a99d; font-size: 0.78rem;">
                                            <span class="btn-text">Reactivar Comentario</span>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <span class="fs-2 d-block mb-2">⭐</span>
                        <p class="mb-0 small fw-semibold">No se encontraron calificaciones.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($calificaciones->hasPages())
            <div class="p-3 px-4 border-top d-flex justify-content-end bg-white" id="pagination-links">
                {{ $calificaciones->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ESTILOS Y SCRIPT DE FILTRADO Y SPINNERS -->
<style>
.text-teal { color: #00a99d !important; }
.transition-all { transition: all 0.2s ease; }
.transition-all:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.active-star {
    background: #111111 !important;
    color: #ffffff !important;
    border-color: #111111 !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
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
document.addEventListener('DOMContentLoaded', function () {
    const starButtons = document.querySelectorAll('.filter-star-btn');
    const inputEstrellas = document.getElementById('filter-estrellas');
    const filterEstado = document.getElementById('filter-estado');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const tableContainer = document.getElementById('table-container');

    function fetchCalificaciones(url = null, triggerElement = null) {
        const estrellas = inputEstrellas.value;
        const estado = filterEstado.value;

        let targetUrl = url || "{{ route('admin.calificaciones.index') }}";
        const queryParams = new URLSearchParams({
            estrellas: estrellas,
            estado: estado
        }).toString();

        if (!url) {
            targetUrl += '?' + queryParams;
        }

        tableContainer.style.opacity = '0.4';

        // Validación de 250ms: El spinner de tijera solo aparece si la petición demora más de lo debido
        let spinnerTimeout = setTimeout(() => {
            if (triggerElement) {
                triggerElement.disabled = true;
                const textSpan = triggerElement.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.dataset.original = textSpan.innerHTML;
                    textSpan.innerHTML = `<i class="bi bi-scissors scissors-spin me-1"></i> Cargando...`;
                }
            }
        }, 250);

        fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            clearTimeout(spinnerTimeout);
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableContainer = doc.getElementById('table-container');

            if (newTableContainer) {
                tableContainer.innerHTML = newTableContainer.innerHTML;
            } else {
                tableContainer.innerHTML = html;
            }
            tableContainer.style.opacity = '1';

            if (triggerElement) {
                triggerElement.disabled = false;
                const textSpan = triggerElement.querySelector('.btn-text');
                if (textSpan && textSpan.dataset.original) {
                    textSpan.innerHTML = textSpan.dataset.original;
                }
            }
        })
        .catch(error => {
            clearTimeout(spinnerTimeout);
            console.error('Error al filtrar calificaciones:', error);
            tableContainer.style.opacity = '1';

            if (triggerElement) {
                triggerElement.disabled = false;
                const textSpan = triggerElement.querySelector('.btn-text');
                if (textSpan && textSpan.dataset.original) {
                    textSpan.innerHTML = textSpan.dataset.original;
                }
            }
        });
    }

    // Evitar selección de texto por dobles clics rápidos en botones
    document.querySelectorAll('.btn-no-select').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            if (e.detail > 1) e.preventDefault();
        });
    });

    // Manejar clics en los botones de estrellas
    starButtons.forEach(button => {
        button.addEventListener('click', function () {
            if (this.disabled) return;

            starButtons.forEach(btn => {
                btn.classList.remove('active-star');
                btn.classList.add('btn-light', 'text-secondary', 'border');
            });
            this.classList.add('active-star');
            this.classList.remove('btn-light', 'text-secondary', 'border');

            inputEstrellas.value = this.getAttribute('data-value');
            fetchCalificaciones(null, this);
        });
    });

    // Filtrado automático al cambiar el estado
    filterEstado.addEventListener('change', () => fetchCalificaciones(null, filterEstado));

    // Botón limpiar filtros
    btnLimpiar.addEventListener('click', function () {
        if (this.disabled) return;

        inputEstrellas.value = '';
        starButtons.forEach(btn => {
            btn.classList.remove('active-star');
            btn.classList.add('btn-light', 'text-secondary', 'border');
        });
        document.querySelector('.filter-star-btn[data-value=""]').classList.add('active-star');
        document.querySelector('.filter-star-btn[data-value=""]').classList.remove('btn-light', 'text-secondary', 'border');

        filterEstado.value = '';
        fetchCalificaciones(null, this);
    });

    // Paginación por AJAX
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchCalificaciones(link.href);
        }
    });

    // SISTEMA ANTI-DOBLE CLIC CON RETRASO DE 250ms Y SPINNER DE TIJERAS
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('form-action-prevent')) {
            const submitBtn = form.querySelector('.btn-with-spinner');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const textSpan = submitBtn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.dataset.original = textSpan.innerHTML;
                    setTimeout(() => {
                        textSpan.innerHTML = `<i class="bi bi-scissors scissors-spin me-1"></i> Procesando...`;
                    }, 250);
                }
            }
        }
    });
});
</script>
@endsection