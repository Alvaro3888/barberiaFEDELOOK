@extends('layouts.public')

@section('content')
<div class="catalog-wrapper py-5">
    <div class="container py-5 mt-4">
        
        {{-- Encabezado con identidad visual --}}
        <div class="text-center mb-5 catalog-header">
            <span class="text-uppercase tracking-widest text-teal fw-bold small d-block mb-2">
                Fede looK • Catálogo Exclusivo
            </span>
            <h1 class="fw-bold display-5 text-white mb-3">Elegí tu estilo</h1>
            <p class="text-white-50 mx-auto lead-custom" style="max-width: 600px;">
                Seleccioná el servicio que prefieras para avanzar directamente con tu reserva personalizada.
            </p>

            {{-- Filtros rápidos interactivos por categoría (Generados dinámicamente con JS) --}}
            @if(isset($serviciosAgrupados) && $serviciosAgrupados->isNotEmpty())
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-4 filter-buttons-container">
                    <button type="button" class="btn-filter active" data-filter="all">
                        <i class="bi bi-grid-fill me-1"></i> Todos
                    </button>
                    @foreach($serviciosAgrupados as $tipoNombre => $servicios)
                        <button type="button" class="btn-filter" data-filter="{{ Str::slug($tipoNombre) }}">
                            {{ $tipoNombre }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Listado de Categorías y Carruseles --}}
        @forelse($serviciosAgrupados as $tipoNombre => $servicios)
            <div class="category-section mb-5" data-category="{{ Str::slug($tipoNombre) }}">
                <div class="d-flex align-items-center mb-4">
                    <div class="category-indicator me-3"></div>
                    <h3 class="fw-bold text-white mb-0">{{ $tipoNombre }}</h3>
                    <div class="flex-grow-1 border-bottom border-white border-opacity-10 ms-3"></div>
                    <span class="badge bg-teal text-white border border-teal rounded-pill px-3 py-2 ms-3 small fw-semibold" style="background-color: var(--petrol) !important;">
                        {{ $servicios->count() }} {{ $servicios->count() == 1 ? 'opción' : 'opciones' }} disponibles
                    </span>
                </div>

                <div class="d-flex flex-row flex-nowrap overflow-x-auto pb-4 gap-3 modern-scrollbar">
                    @foreach($servicios as $servicio)
                        @include('usuario.servicios.partials.tarjeta-servicio', ['servicio' => $servicio])
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-5 my-5 bg-card-dark rounded-4 border border-white border-opacity-10">
                <i class="bi bi-scissors fs-1 text-teal d-block mb-3"></i>
                <h4 class="text-white fw-bold">No hay servicios disponibles</h4>
                <p class="text-white-50 mb-0">El administrador aún no ha cargado servicios activos.</p>
            </div>
        @endforelse

    </div>
</div>

<style>
/* PALETA DE COLORES Y ESTILOS GENERALES DEL CATÁLOGO (Igual al Index) */
.catalog-wrapper {
    background-color: #0f1214;
    min-height: 85vh;
    position: relative;
    overflow: hidden;
}

/* Efecto de luz ambiental de fondo sutil */
.catalog-wrapper::before {
    content: '';
    position: absolute;
    top: -100px;
    left: 50%;
    transform: translateX(-50%);
    width: 600px;
    height: 300px;
    background: radial-gradient(circle, rgba(0, 169, 157, 0.12) 0%, rgba(15, 18, 20, 0) 70%);
    z-index: 0;
    pointer-events: none;
}

.catalog-header, .category-section {
    position: relative;
    z-index: 1;
}

.text-teal {
    color: #00a99d !important;
}

.bg-teal {
    background-color: #00a99d !important;
}

.border-teal {
    border-color: #00a99d !important;
}

.tracking-widest {
    letter-spacing: 0.2em;
}

.lead-custom {
    font-size: 0.95rem;
}

/* BARRA INDICADORA DE CATEGORÍA */
.category-indicator {
    width: 6px;
    height: 24px;
    background: linear-gradient(180deg, #00a99d 0%, #16849f 100%);
    border-radius: 4px;
}

/* BOTONES DE FILTRO INTERACTIVO (JS) */
.btn-filter {
    background: #171b1e;
    color: #a0a0a0;
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 0.5rem 1.25rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.btn-filter:hover {
    color: #ffffff;
    border-color: rgba(0, 169, 157, 0.4);
    transform: translateY(-2px);
}

.btn-filter.active {
    background: #00a99d;
    color: #ffffff;
    border-color: #00a99d;
    box-shadow: 0 4px 15px rgba(0, 169, 157, 0.35);
}

/* ESTILOS DE TARJETAS (Compartidos con el Partial) */
.bg-card-dark {
    background-color: #171b1e;
    border: 1px solid rgba(255, 255, 255, 0.07) !important;
    transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.3s ease;
}

.modern-card-hover:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
    border-color: rgba(0, 169, 157, 0.45) !important;
}

.service-img {
    transition: transform 0.4s ease;
}

.modern-card-hover:hover .service-img {
    transform: scale(1.08);
}

.btn-action-circle {
    width: 32px;
    height: 32px;
    background: rgba(0, 169, 157, 0.12);
    color: #00a99d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.modern-card-hover:hover .btn-action-circle {
    background: #00a99d;
    color: #ffffff;
    transform: translateX(3px);
}

/* SCROLLBAR MODERNO */
.modern-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #00a99d #171b1e;
}

.modern-scrollbar::-webkit-scrollbar {
    height: 5px;
}

.modern-scrollbar::-webkit-scrollbar-track {
    background: #121517;
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb {
    background-color: #00a99d;
    border-radius: 10px;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

{{-- SCRIPT JAVASCRIPT PARA FILTRAR DINÁMICAMENTE LAS CATEGORÍAS SIN RECARGAR --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".btn-filter");
    const categorySections = document.querySelectorAll(".category-section");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Remover clase active de todos los botones y ponérsela al clickeado
            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            const filterValue = this.getAttribute("data-filter");

            categorySections.forEach(section => {
                const categoryName = section.getAttribute("data-category");

                if (filterValue === "all" || categoryName === filterValue) {
                    section.style.display = "block";
                    // Pequeña animación de aparición
                    section.style.animation = "fadeInCatalog 0.4s ease forwards";
                } else {
                    section.style.display = "none";
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeInCatalog {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".btn-filter");
    const categorySections = document.querySelectorAll(".category-section");

    // Capturar si viene un tipo de servicio seleccionado desde la URL (o desde el backend)
    const urlParams = new URLSearchParams(window.location.search);
    const selectedTipoId = urlParams.get('tipo');

    function aplicarFiltro(filterValue) {
        let matchEncontrado = false;

        filterButtons.forEach(btn => {
            if (btn.getAttribute("data-filter") === filterValue) {
                btn.classList.add("active");
                matchEncontrado = true;
            } else {
                btn.classList.remove("active");
            }
        });

        categorySections.forEach(section => {
            const categoryName = section.getAttribute("data-category");

            if (filterValue === "all" || categoryName === filterValue) {
                section.style.display = "block";
                section.style.animation = "fadeInCatalog 0.4s ease forwards";
            } else {
                section.style.display = "none";
            }
        });

        return matchEncontrado;
    }

    // Si viene un parámetro tipo por URL, buscar si coincide con la categoría
    if (selectedTipoId) {
        @php
            $tipoBuscado = \App\Models\TipoServicio::find($tipoId ?? null);
            $slugBuscado = $tipoBuscado ? Str::slug($tipoBuscado->nombre) : '';
        @endphp

        const slugTarget = "{{ $slugBuscado }}";
        
        if (slugTarget) {
            aplicarFiltro(slugTarget);
        }
    }
    
    // Evento click en los botones de filtro
    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const filterValue = this.getAttribute("data-filter");
            aplicarFiltro(filterValue);
        });
    });
});
</script>
@endsection