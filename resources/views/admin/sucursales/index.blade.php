@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <!-- Encabezado con Animación -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 pb-3 border-bottom animate-fade-in">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="p-2 text-white rounded-3 shadow-sm brand-icon-container" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <!-- Logo corregido y con proporciones exactas -->
                    <img src="{{ asset('logos/cortebarba2.png') }}" alt="Logo Sucursales" class="logo-header-inline">
                </span>
                Listado de Sucursales
            </h1>
            <p class="text-muted mb-0">Gestiona la información, ubicación e imágenes de todas las sucursales.</p>
        </div>
        <a href="{{ route('admin.sucursales.create') }}" class="btn px-4 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center gap-2 btn-video-glow rounded-3 text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
            </svg>
            Nueva sucursal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 alert-dismissible fade show" role="alert" style="background-color: #d1fae5; color: #065f46;">
            <div class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjeta de Filtros de Búsqueda Estilizada -->
    <div class="card border-0 shadow-sm p-4 mb-4 rounded-4 bg-white">
        <form action="{{ route('admin.sucursales.index') }}" method="GET" class="row g-3 align-items-end" id="formFiltroSucursal">
            <div class="col-md-5">
                <label for="nombre" class="form-label small fw-bold text-uppercase text-muted">Filtrar por Nombre</label>
                <!-- Input de nombre 100% blindado contra números -->
                <input type="text" name="nombre" id="nombre" class="form-control form-control-lg border bg-light py-2 fs-6 input-solo-letras" value="{{ request('nombre') }}" placeholder="Ej: Centro, Norte..." autocomplete="off">
            </div>
            <div class="col-md-5">
                <label for="direccion" class="form-label small fw-bold text-uppercase text-muted">Filtrar por Dirección</label>
                <input type="text" name="direccion" id="direccion" class="form-control form-control-lg border bg-light py-2 fs-6" value="{{ request('direccion') }}" placeholder="Ej: Av. San Martín...">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-lg w-100 py-2 shadow-sm fw-bold fs-6">Buscar</button>
                @if(request('nombre') || request('direccion'))
                    <a href="{{ route('admin.sucursales.index') }}" class="btn btn-light btn-lg py-2 border text-secondary font-weight-bold btn-hover-scale px-3" title="Limpiar filtros">✕</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabla de Sucursales Moderna -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 ps-4">Imagen</th>
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Dirección / Teléfono</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="py-3 text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sucursales as $sucursal)
                            <tr class="transition-row">
                                <td class="ps-4 py-3" style="width: 80px;">
                                    <div class="rounded-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center shadow-xs" style="width: 50px; height: 50px;">
                                        @if($sucursal->imagen)
                                            <img src="{{ asset('storage/' . $sucursal->imagen) }}" alt="{{ $sucursal->nombre }}" class="w-100 h-100 object-fit-cover">
                                        @elseif($sucursal->imagen_url)
                                            <img src="{{ $sucursal->imagen_url }}" alt="{{ $sucursal->nombre }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <span class="fs-5">💈</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="fw-bold text-dark d-block">{{ $sucursal->nombre }}</span>
                                    @if($sucursal->horarios)
                                        <span class="text-muted small">🕒 {{ $sucursal->horarios }}</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="text-secondary small mb-1">📍 {{ $sucursal->direccion }}</div>
                                    <div class="text-muted small">📞 {{ $sucursal->telefono }}</div>
                                </td>
                                <td class="text-center py-3">
                                    @if($sucursal->activo)
                                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">Activa</span>
                                    @else
                                        <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">Inactiva</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        <!-- Editar -->
                                        <a href="{{ route('admin.sucursales.edit', $sucursal->id) }}" class="btn-action btn-action-info" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                                        </a>

                                        @if($sucursal->activo)
                                            <form action="{{ route('admin.sucursales.destroy', $sucursal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas desactivar esta sucursal?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-danger" title="Desactivar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.sucursales.activate', $sucursal->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas activar esta sucursal?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-action btn-action-success" title="Activar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <span class="fs-1 d-block mb-2">🏢</span>
                                        <h5 class="fw-semibold text-dark">No se encontraron sucursales</h5>
                                        <p class="small mb-0">Prueba ajustando los filtros de búsqueda o crea una nueva sucursal.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos correctos y proporcionales para el logo integrado con animación */
    .logo-header-inline {
        width: 24px;
        height: 24px;
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

    .card-modern {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
        border: none;
    }
    .transition-row {
        transition: background-color 0.2s ease;
    }
    .transition-row:hover {
        background-color: #fafbfd !important;
    }
    .btn-hover-scale {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .btn-hover-scale:hover {
        transform: translateY(-1px);
        background-color: #e2e8f0 !important;
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.5);
    }
    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: #f1f3f5;
        cursor: pointer;
    }
    .btn-action-info {
        color: #0dcaf0;
        border-color: #e0fcfd;
        background: #eefcfd;
    }
    .btn-action-info:hover {
        background-color: #0dcaf0;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 202, 240, 0.3);
    }
    .btn-action-danger {
        color: #dc3545;
        border-color: #fde8e8;
        background: #fef2f2;
    }
    .btn-action-danger:hover {
        background-color: #dc3545;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }
    .btn-action-success {
        color: #198754;
        border-color: #e3f8ec;
        background: #edf7f2;
    }
    .btn-action-success:hover {
        background-color: #198754;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
    }
    .form-control:focus {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1) !important;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>

<!-- Script dinámico para el logo y el blindaje estricto del input de nombre -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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

    // Blindaje estricto 100% para el input de filtro por nombre (Bloqueo de números)
    const inputNombre = document.getElementById('nombre');
    if (inputNombre) {
        inputNombre.addEventListener('keypress', function(e) {
            const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(charStr)) {
                e.preventDefault();
            }
        });

        inputNombre.addEventListener('paste', function(e) {
            e.preventDefault();
            const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
            const textoLimpio = textoPegado.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
            this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
        });

        inputNombre.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }
});
</script>
@endsection