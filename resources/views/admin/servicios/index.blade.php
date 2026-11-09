@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 py-md-5 px-lg-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- Header moderno y Buscador -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom animate-fade-in gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="p-2 text-white rounded-3 shadow-sm brand-icon-container btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                <img src="{{ asset('logos/cortebarba2.png') }}" alt="Logo" class="logo-header-inline">
            </span>
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">Catálogo de Servicios</h1>
                <p class="text-muted small mb-0">Gestión avanzada, filtrado y visualización de todo el catálogo de barbería.</p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Buscador en tiempo real 100% blindado (Solo letras) -->
            <div class="input-group shadow-xs rounded-pill overflow-hidden bg-white border" style="width: 280px;">
                <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorServicios" class="form-control border-0 ps-2 py-2 fs-6 shadow-none" placeholder="Buscar servicio..." autocomplete="off">
            </div>
            <a href="{{ route('admin.servicios.create') }}" class="btn px-4 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center gap-2 btn-video-glow rounded-pill text-white text-nowrap btn-no-select transition-all" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">
                <i class="bi bi-plus-lg"></i> Nuevo servicio
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 animate-fade-in rounded-4" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- 🔹 ORGANIZACIÓN POR TIPO DE SERVICIO (ACORDEÓN MODERNO) -->
    @php
        $serviciosOrdenados = isset($servicios) ? $servicios->sortByDesc('id') : collect();
        $serviciosAgrupados = $serviciosOrdenados->groupBy(function($servicio) {
            return $servicio->tipoServicio ? $servicio->tipoServicio->nombre : 'Sin Categoría / Tipo';
        });
    @endphp

    <div class="accordion" id="accordionServicios">
        @forelse($serviciosAgrupados as $tipoNombre => $serviciosDelTipo)
            <div class="card border-0 shadow-sm mb-3 rounded-4 overflow-hidden card-modern bg-white">
                <!-- Cabecera de la Sección / Grupo -->
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#collapse-{{ Str::slug($tipoNombre) }}" 
                     style="cursor: pointer;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-circle text-teal" style="width: 36px; height: 36px; background: #eefcfd; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-folder2-open fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark fs-6">{{ $tipoNombre }}</h5>
                        <span class="badge bg-teal-soft text-teal border border-teal-subtle ms-2 px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.7rem;">{{ $serviciosDelTipo->count() }} servicios</span>
                    </div>
                    <i class="bi bi-chevron-down text-muted transition-chevron"></i>
                </div>

                <!-- Contenido de la Sección -->
                <div id="collapse-{{ Str::slug($tipoNombre) }}" class="collapse show" data-bs-parent="#accordionServicios">
                    <div class="card-body p-4" style="background-color: #f8fafc;">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 contenedor-tarjetas">
                            @foreach($serviciosDelTipo as $servicio)
                                <div class="col item-servicio" data-nombre="{{ strtolower($servicio->nombre) }}">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden bg-white d-flex flex-column card-servicio-hover">
                                        
                                        <div class="row g-0 flex-grow-1">
                                            <!-- Contenedor de Imagen Proporcionado -->
                                            <div class="col-4 position-relative bg-light" style="min-height: 120px;">
                                                @if($servicio->imagen)
                                                    <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre }}" class="w-100 h-100 object-fit-cover">
                                                @elseif($servicio->imagen_url)
                                                    <img src="{{ $servicio->imagen_url }}" alt="{{ $servicio->nombre }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted bg-light">
                                                        <i class="bi bi-image fs-4 opacity-50"></i>
                                                    </div>
                                                @endif
                                                <span class="position-absolute top-0 start-0 m-2 badge {{ $servicio->activo ? 'bg-success' : 'bg-secondary' }} px-2 py-1 shadow-xs rounded-pill btn-no-select" style="font-size: 0.60rem;">
                                                    {{ $servicio->activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </div>

                                            <!-- Información del Servicio -->
                                            <div class="col-8 d-flex flex-column justify-content-between p-3">
                                                <div>
                                                    <div class="text-muted text-truncate mb-1 small fw-medium" style="font-size: 0.75rem;">
                                                        <i class="bi bi-geo-alt me-1 text-teal"></i>{{ $servicio->sucursal ? $servicio->sucursal->nombre : 'Sin sucursal' }}
                                                    </div>
                                                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $servicio->nombre }}">{{ $servicio->nombre }}</h6>
                                                    @if($servicio->descripcion)
                                                        <p class="text-muted text-truncate small mb-0" style="font-size: 0.75rem;">{{ $servicio->descripcion }}</p>
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top border-light">
                                                    <div>
                                                        <span class="text-teal fw-bold fs-6">${{ number_format($servicio->precio ?? $servicio->precio_actual, 0, ',', '.') }}</span>
                                                        <span class="text-muted d-block" style="font-size: 0.7rem;"><i class="bi bi-clock me-1"></i>{{ $servicio->duracion_minutos ?? $servicio->duracion ?? 30 }} min</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Botones de Acción Estilizados en la Base con Spinner de Tijeras -->
                                        <div class="card-footer bg-white border-top py-2.5 px-3 d-flex gap-2">
                                            <a href="{{ route('admin.servicios.edit', $servicio->id) }}" class="btn btn-outline-info btn-sm flex-grow-1 py-1.5 fw-semibold rounded-pill d-flex align-items-center justify-content-center gap-1 btn-no-select" style="font-size: 0.75rem;">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            @if($servicio->activo)
                                                <form action="{{ route('admin.servicios.destroy', $servicio->id) }}" method="POST" class="flex-grow-1 m-0 form-action-prevent" onsubmit="return confirm('¿Seguro que deseas desactivar este servicio?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-1.5 fw-semibold rounded-pill d-flex align-items-center justify-content-center gap-1 btn-with-scissors btn-no-select" style="font-size: 0.75rem;">
                                                        <span class="btn-text"><i class="bi bi-eye-slash"></i> Desactivar</span>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.servicios.activate', $servicio->id) }}" method="POST" class="flex-grow-1 m-0 form-action-prevent" onsubmit="return confirm('¿Seguro que deseas activar este servicio?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-outline-success btn-sm w-100 py-1.5 fw-semibold rounded-pill d-flex align-items-center justify-content-center gap-1 btn-with-scissors btn-no-select" style="font-size: 0.75rem;">
                                                        <span class="btn-text"><i class="bi bi-eye"></i> Activar</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 card border-0 shadow-sm rounded-4 bg-white">
                <div class="text-muted mb-3"><i class="bi bi-inbox fs-1"></i></div>
                <h5 class="text-muted">No hay servicios registrados en el sistema.</h5>
                <p class="small text-muted mb-0">Comienza creando un nuevo servicio desde el botón superior.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    .text-teal { color: #0d9488 !important; }
    .transition-all { transition: all 0.2s ease; }
    .card-modern {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
        border: none;
    }
    .card-servicio-hover {
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid #f1f3f5 !important;
    }
    .card-servicio-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.75rem 1.5rem rgba(13, 148, 136, 0.1) !important;
        border-color: #ccfbf1 !important;
    }
    .logo-header-inline {
        width: 20px;
        height: 20px;
        object-fit: contain;
        display: block;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .brand-icon-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .brand-icon-container:hover .logo-header-inline {
        transform: scale(1.18) rotate(-8deg);
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
    }
    .bg-teal-soft {
        background-color: #f0fdfa !important;
    }
    .border-teal-subtle {
        border-color: #ccfbf1 !important;
    }
    .btn-no-select {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }
    /* Spinner de Tijeras Animadas */
    .scissors-loader {
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

<!-- Script para Buscador, Anti-doble clic y Spinner de Tijeras -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscador = document.getElementById('buscadorServicios');
    const itemsServicio = document.querySelectorAll('.item-servicio');
    const brandContainer = document.querySelector('.brand-icon-container');
    const inlineLogo = document.querySelector('.logo-header-inline');

    if (brandContainer && inlineLogo) {
        brandContainer.addEventListener('click', function() {
            inlineLogo.style.transform = 'scale(0.8) rotate(360deg)';
            setTimeout(() => {
                inlineLogo.style.transform = 'scale(1) rotate(0deg)';
            }, 400);
        });
    }

    // Prevenir selección de texto accidental en botones
    document.querySelectorAll('.btn-no-select').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            if (e.detail > 1) e.preventDefault();
        });
    });

    // SISTEMA ANTI-DOBLE CLIC Y SPINNER DE TIJERAS INTELIGENTE (Retraso de 250ms)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('form-action-prevent')) {
            const submitBtn = form.querySelector('.btn-with-scissors');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const textSpan = submitBtn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.dataset.original = textSpan.innerHTML;
                    
                    let spinnerTimer = setTimeout(() => {
                        textSpan.innerHTML = `<i class="bi bi-scissors scissors-loader me-1"></i> Procesando...`;
                    }, 250);

                    window.addEventListener('beforeunload', () => clearTimeout(spinnerTimer));
                }
            }
        }
    });

    if (inputBuscador) {
        // Bloqueo de números por tecla en el buscador
        inputBuscador.addEventListener('keypress', function(e) {
            const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(charStr)) {
                e.preventDefault();
            }
        });

        // Limpieza de pegado de portapapeles
        inputBuscador.addEventListener('paste', function(e) {
            e.preventDefault();
            const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
            const textoLimpio = textoPegado.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
            this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
            this.dispatchEvent(new Event('input'));
        });

        // Filtrado en tiempo real
        inputBuscador.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            const textoBusqueda = this.value.toLowerCase().trim();

            itemsServicio.forEach(item => {
                const nombreServicio = item.getAttribute('data-nombre');
                if (nombreServicio.includes(textoBusqueda)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection