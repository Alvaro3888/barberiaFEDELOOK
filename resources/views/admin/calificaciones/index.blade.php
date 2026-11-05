@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-lg-4">
    
    <!-- ENCABEZADO ESTILO SUCURSALES -->
    <div class="p-4 p-lg-5 rounded-4 text-white mb-4 position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #111111 0%, #1f2937 100%);">
        <div class="position-relative z-2">
            <span class="badge px-3 py-2 rounded-pill text-uppercase mb-2 shadow-sm fw-bold" style="background: #00a99d; color: #fff; letter-spacing: 1.5px; font-size: 0.7rem;">
                Módulo de Moderación
            </span>
            <h1 class="display-6 fw-bold mb-1 text-white" style="letter-spacing: -0.01em;">
                Calificaciones y Reseñas
            </h1>
            <p class="text-white-50 mb-0 small" style="max-width: 600px; font-size: 0.95rem;">
                Administrá y filtrá las opiniones recibidas. Mantené la calidad de la plataforma dando de baja reseñas malintencionadas de forma instantánea.
            </p>
        </div>
    </div>

    <!-- TARJETA DE FILTROS EN TIEMPO REAL -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <form id="filter-form" class="row g-3 align-items-center">
                
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1">Filtrar por Estrellas</label>
                    <select name="estrellas" id="filter-estrellas" class="form-select rounded-3 border bg-light py-2 px-3 shadow-none">
                        <option value="">Todas las puntuaciones (1 a 5)</option>
                        <option value="5" {{ request('estrellas') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Estrellas)</option>
                        <option value="4" {{ request('estrellas') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Estrellas)</option>
                        <option value="3" {{ request('estrellas') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 Estrellas)</option>
                        <option value="2" {{ request('estrellas') == '2' ? 'selected' : '' }}>⭐⭐ (2 Estrellas)</option>
                        <option value="1" {{ request('estrellas') == '1' ? 'selected' : '' }}>⭐ (1 Estrella)</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold text-dark mb-1">Estado de Visibilidad</label>
                    <select name="estado" id="filter-estado" class="form-select rounded-3 border bg-light py-2 px-3 shadow-none">
                        <option value="">Todos los estados</option>
                        <option value="activos" {{ request('estado') == 'activos' ? 'selected' : '' }}>Visibles en la web (Activos)</option>
                        <option value="ocultos" {{ request('estado') == 'ocultos' ? 'selected' : '' }}>Dados de baja (Ocultos)</option>
                    </select>
                </div>

                <div class="col-md-2 mt-md-4 text-md-end">
                    <button type="button" id="btn-limpiar" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 fw-bold w-100">
                        Limpiar
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- CONTENEDOR DE LA TABLA (AJAX & RESPONSIVE) -->
    <div id="table-container" class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white position-relative">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-4 rounded-4 shadow-sm border-0" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-uppercase text-secondary" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                    <tr>
                        <th class="py-3 ps-4">Cliente</th>
                        <th class="py-3">Puntuación</th>
                        <th class="py-3">Comentario</th>
                        <th class="py-3">Servicio / Barbero</th>
                        <th class="py-3">Estado</th>
                        <th class="py-3 text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($calificaciones as $cal)
                        <tr class="{{ !$cal->activo ? 'bg-light text-muted' : '' }}" style="transition: background-color 0.2s ease;">
                            <td class="ps-4 py-3 fw-bold text-dark">
                                {{ $cal->cliente->nombre ?? $cal->cliente->name ?? 'Cliente Anónimo' }}
                            </td>
                            <td class="py-3">
                                <span class="badge fw-bold px-3 py-2 rounded-pill shadow-sm" style="background: rgba(255, 193, 7, 0.15); color: #856404; font-size: 0.8rem;">
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
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(0, 169, 157, 0.12); color: #00a99d; font-size: 0.75rem;">
                                        Visible
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(220, 53, 69, 0.12); color: #dc3545; font-size: 0.75rem;">
                                        Dado de baja
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <form action="{{ route('admin.calificaciones.toggle', $cal->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($cal->activo)
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold transition-all" style="background: #fff; border: 1px solid #dc3545; color: #dc3545; font-size: 0.78rem;" onclick="return confirm('¿Seguro que querés dar de baja este comentario? Ya no se mostrará en la web pública.')">
                                            Dar de baja
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 fw-bold transition-all" style="background: #00a99d; border: 1px solid #00a99d; color: #fff; font-size: 0.78rem;">
                                            Reactivar
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

        @if($calificaciones->hasPages())
            <div class="p-3 px-4 border-top d-flex justify-content-end bg-white" id="pagination-links">
                {{ $calificaciones->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ESTILOS Y SCRIPT DE FILTRADO EN VIVO -->
<style>
.text-teal { color: #00a99d !important; }
.transition-all { transition: all 0.2s ease; }
.transition-all:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterEstrellas = document.getElementById('filter-estrellas');
    const filterEstado = document.getElementById('filter-estado');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const tableContainer = document.getElementById('table-container');

    function fetchCalificaciones(url = null) {
        const estrellas = filterEstrellas.value;
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

        fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Extraemos solo el contenido dinámico si devuelve la página completa, o actualizamos directo si devuelve parcial
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableContainer = doc.getElementById('table-container');

            if (newTableContainer) {
                tableContainer.innerHTML = newTableContainer.innerHTML;
            } else {
                tableContainer.innerHTML = html;
            }
            tableContainer.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error al filtrar calificaciones:', error);
            tableContainer.style.opacity = '1';
        });
    }

    // Filtrado automático instantáneo al cambiar selectores
    filterEstrellas.addEventListener('change', () => fetchCalificaciones());
    filterEstado.addEventListener('change', () => fetchCalificaciones());

    // Botón limpiar filtros
    btnLimpiar.addEventListener('click', function () {
        filterEstrellas.value = '';
        filterEstado.value = '';
        fetchCalificaciones();
    });

    // Paginación por AJAX
    tableContainer.addEventListener('click', function (e) {
        const link = e.target.closest('#pagination-links a');
        if (link) {
            e.preventDefault();
            fetchCalificaciones(link.href);
        }
    });
});
</script>
@endsection