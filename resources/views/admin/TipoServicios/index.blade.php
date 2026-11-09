@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- Encabezado de la Sección -->
    <div class="d-flex align-items-center mb-4 pb-3 border-bottom animate-fade-in">
        <div class="d-flex align-items-center gap-3">
            <span class="p-2 text-white rounded-3 shadow-sm brand-icon-container btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                <img src="{{ asset('logos/cortebarba2.png') }}" alt="Logo" class="logo-header-inline">
            </span>
            <div>
                <h1 class="h4 fw-bold text-dark mb-1">Tipos de Servicio</h1>
                <p class="text-muted small mb-0">Gestiona, crea y edita las categorías de servicios de forma integrada.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center animate-fade-in" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div><span>{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 animate-fade-in" role="alert" style="background-color: #fef2f2; color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
            <span class="fw-bold small">Por favor corrige los siguientes errores:</span>
            <ul class="mb-0 ps-3 small mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- DISTRIBUCIÓN FLUIDA: Tabla a la IZQUIERDA (col-lg-8) y Formulario Compacto a la DERECHA (col-lg-4) -->
    <div class="row g-3 align-items-start">
        
        <!-- COLUMNA IZQUIERDA: Listado (Tabla Desktop y Tarjetas Móvil) -->
        <div class="col-12 col-lg-8 col-xl-9">
            
            <!-- ================= VISTA DESKTOP (TABLA FLUIDA SIN DESBORDES) ================= -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-modern bg-white d-none d-md-block mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Listado Registrado</h6>
                    <span class="badge bg-teal-soft text-teal px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.75rem;">{{ count($tipos) }} Total</span>
                </div>
                <div class="card-body p-0">
                    <!-- Eliminamos el desborde forzado permitiendo que la tabla use el 100% fluido -->
                    <div class="table-responsive w-100" style="max-height: 550px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 w-100" style="table-layout: fixed;">
                            <thead class="bg-light text-uppercase font-weight-bold text-muted border-bottom sticky-top shadow-xs" style="font-size: 0.7rem; letter-spacing: 0.5px; z-index: 5; background-color: #f8f9fa !important;">
                                <tr>
                                    <th class="py-3 ps-4 bg-light" style="width: 42%;">Nombre / Descripción</th>
                                    <th class="py-3 bg-light" style="width: 25%;">Sucursal</th>
                                    <th class="py-3 text-center bg-light" style="width: 15%;">Estado</th>
                                    <th class="py-3 text-end pe-4 bg-light" style="width: 18%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tipos as $tipo)
                                    <tr class="transition-row">
                                        <td class="ps-4 py-3 text-truncate">
                                            <span class="fw-bold text-dark d-block text-truncate">{{ $tipo->nombre }}</span>
                                            @if($tipo->descripcion)
                                                <span class="text-muted small text-truncate d-block" style="max-width: 100%;">{{ $tipo->descripcion }}</span>
                                            @else
                                                <span class="text-muted small fst-italic">Sin descripción</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-truncate">
                                            <span class="badge bg-light text-secondary border px-2 py-1 btn-no-select text-truncate mw-100" style="font-size: 0.75rem;">
                                                <i class="bi bi-shop me-1 text-teal"></i> {{ $tipo->sucursal ? $tipo->sucursal->nombre : 'Sin asignar' }}
                                            </span>
                                        </td>
                                        <td class="text-center py-3">
                                            @if($tipo->activo)
                                                <span class="badge bg-success text-white px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.68rem;">Activo</span>
                                            @else
                                                <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.68rem;">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <div class="d-flex justify-content-end align-items-center gap-2">
                                                <!-- Botón para Editar In-Situ -->
                                                <button type="button" 
                                                        class="btn-action btn-action-info btn-editar btn-no-select" 
                                                        title="Editar"
                                                        data-id="{{ $tipo->id }}"
                                                        data-nombre="{{ $tipo->nombre }}"
                                                        data-descripcion="{{ $tipo->descripcion }}"
                                                        data-sucursal="{{ $tipo->sucursal_id }}"
                                                        data-url="{{ route('admin.TipoServicios.update', $tipo->id) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                                                </button>

                                                @if($tipo->activo)
                                                    <form action="{{ route('admin.TipoServicios.destroy', $tipo->id) }}" method="POST" class="d-inline form-action-prevent" onsubmit="return confirm('¿Seguro que deseas desactivar este tipo de servicio?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action btn-action-danger btn-with-scissors btn-no-select" title="Desactivar">
                                                            <span class="btn-text">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                                                            </span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.TipoServicios.activate', $tipo->id) }}" method="POST" class="d-inline form-action-prevent" onsubmit="return confirm('¿Seguro que deseas activar este tipo de servicio?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn-action btn-action-success btn-with-scissors btn-no-select" title="Activar">
                                                            <span class="btn-text">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <div class="py-3">
                                                <span class="fs-2 d-block mb-2">📋</span>
                                                <h6 class="fw-semibold text-dark">No hay tipos de servicio registrados</h6>
                                                <p class="small mb-0">Utiliza el formulario lateral para registrar el primero.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ================= VISTA MÓVIL (TARJETAS TÁCTILES) ================= -->
            <div class="d-block d-md-none">
                <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                    <span class="text-uppercase text-secondary fw-bold small" style="letter-spacing: 0.5px;">Listado Registrado</span>
                    <span class="badge bg-teal-soft text-teal px-2 py-0.5 rounded-pill btn-no-select" style="font-size: 0.7rem;">{{ count($tipos) }} Total</span>
                </div>
                <div class="row g-3">
                    @forelse($tipos as $tipo)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white card-modern">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $tipo->nombre }}</h6>
                                        @if($tipo->descripcion)
                                            <p class="text-muted small mb-1">{{ $tipo->descripcion }}</p>
                                        @else
                                            <p class="text-muted small fst-italic mb-1">Sin descripción</p>
                                        @endif
                                    </div>
                                    <span class="badge bg-{{ $tipo->activo ? 'success' : 'secondary' }} text-white px-2 py-0.5 rounded-pill btn-no-select" style="font-size: 0.65rem;">
                                        {{ $tipo->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>

                                <div class="mb-3 small text-secondary">
                                    <span class="badge bg-light text-secondary border px-2 py-1 btn-no-select" style="font-size: 0.7rem;">
                                        <i class="bi bi-shop me-1 text-teal"></i> {{ $tipo->sucursal ? $tipo->sucursal->nombre : 'Sin asignar' }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-end gap-2 border-top pt-2">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold btn-editar btn-no-select" 
                                            style="font-size: 0.75rem;"
                                            data-id="{{ $tipo->id }}"
                                            data-nombre="{{ $tipo->nombre }}"
                                            data-descripcion="{{ $tipo->descripcion }}"
                                            data-sucursal="{{ $tipo->sucursal_id }}"
                                            data-url="{{ route('admin.TipoServicios.update', $tipo->id) }}">
                                        Editar
                                    </button>

                                    @if($tipo->activo)
                                        <form action="{{ route('admin.TipoServicios.destroy', $tipo->id) }}" method="POST" class="d-inline form-action-prevent" onsubmit="return confirm('¿Desactivar?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold btn-with-scissors btn-no-select" style="font-size: 0.75rem;"><span class="btn-text">Desactivar</span></button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.TipoServicios.activate', $tipo->id) }}" method="POST" class="d-inline form-action-prevent" onsubmit="return confirm('¿Activar?');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold btn-with-scissors btn-no-select" style="font-size: 0.75rem;"><span class="btn-text">Activar</span></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted bg-white rounded-4 shadow-sm">
                            <span class="fs-2 d-block mb-2">📋</span>
                            <p class="small mb-0">No hay tipos de servicio registrados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- COLUMNA DERECHA: Formulario Dinámico Ultra Compacto -->
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-modern bg-white sticky-top" style="top: 20px; z-index: 10;">
                <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 id="form-title" class="text-uppercase text-dark fw-bold mb-0 d-flex align-items-center gap-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-plus-circle text-teal fs-6" id="form-icon"></i> <span id="form-title-text">Nuevo Tipo</span>
                    </h6>
                    <!-- Botón para cancelar edición -->
                    <button type="button" id="btn-cancel-edit" class="btn btn-sm btn-outline-secondary d-none px-2 py-0.5 rounded-pill btn-no-select" style="font-size: 0.65rem;" title="Cancelar edición">Cancelar</button>
                </div>
                <div class="card-body p-3 bg-white">
                    <form action="{{ route('admin.TipoServicios.store') }}" method="POST" id="formTipoServicio" class="form-action-prevent" novalidate>
                        @csrf
                        <div id="method-container"></div>

                        <div class="row g-2">
                            <div class="col-12">
                                <label for="nombre" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 0.72rem;">NOMBRE <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control form-control-sm border bg-light py-1.5 rounded-3 shadow-none fs-6" placeholder="Ej: Cortes Clásicos" required autocomplete="off">
                                <div class="invalid-feedback">El nombre solo debe contener letras y espacios.</div>
                            </div>

                            <div class="col-12">
                                <label for="sucursal_id" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 0.72rem;">SUCURSAL <span class="text-danger">*</span></label>
                                <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm border bg-light py-1.5 rounded-3 shadow-none fs-6" required>
                                    <option value="">Selecciona una sucursal...</option>
                                    @foreach(\App\Models\Sucursal::all() as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Selecciona una sucursal válida.</div>
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-semibold text-secondary small mb-1" style="font-size: 0.72rem;">DESCRIPCIÓN</label>
                                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm border bg-light py-1.5 rounded-3 shadow-none fs-6" placeholder="Breve detalle opcional..." value="{{ old('descripcion') }}" autocomplete="off">
                                <div class="invalid-feedback">La descripción solo debe contener letras y espacios.</div>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="submit" id="btn-submit" class="btn w-100 py-2 fw-semibold shadow-sm btn-video-glow rounded-pill text-white btn-with-scissors btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none; font-size: 0.82rem;">
                                    <span class="btn-text"><i class="bi bi-check-lg me-1"></i> <span id="btn-text-content">Registrar Tipo</span></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
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
    .card-modern {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.05) !important;
        border: none;
    }
    .transition-row {
        transition: background-color 0.2s ease;
    }
    .transition-row:hover {
        background-color: #fafbfd !important;
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.01);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
    }
    .text-teal {
        color: #0d9488 !important;
    }
    .bg-teal-soft {
        background-color: #f0fdfa !important;
    }
    .btn-action {
        width: 32px;
        height: 32px;
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
    .form-control:focus, .form-select:focus {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1) !important;
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
document.addEventListener("DOMContentLoaded", function() {
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

    // Lógica dinámica para cambiar el formulario entre Crear y Editar
    const form = document.getElementById('formTipoServicio');
    const formTitleText = document.getElementById('form-title-text');
    const formIcon = document.getElementById('form-icon');
    const btnTextContent = document.getElementById('btn-text-content');
    const btnCancelEdit = document.getElementById('btn-cancel-edit');
    const methodContainer = document.getElementById('method-container');

    const inputNombre = document.getElementById('nombre');
    const inputDescripcion = document.getElementById('descripcion');
    const selectSucursal = document.getElementById('sucursal_id');

    const storeRoute = "{{ route('admin.TipoServicios.store') }}";

    // Función auxiliar para blindar inputs contra números
    function blindarSoloLetras(elemento) {
        if (!elemento) return;

        elemento.addEventListener('keypress', function(e) {
            const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(charStr)) {
                e.preventDefault();
            }
        });

        elemento.addEventListener('paste', function(e) {
            e.preventDefault();
            const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
            const textoLimpio = textoPegado.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
            this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
        });

        elemento.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    // Aplicar blindaje a Nombre y Descripción
    blindarSoloLetras(inputNombre);
    blindarSoloLetras(inputDescripcion);

    // Manejar clic en botones de editar (Scroll automático suave al formulario derecho)
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.addEventListener('click', function() {
            const updateUrl = this.getAttribute('data-url');
            const nombre = this.getAttribute('data-nombre');
            const descripcion = this.getAttribute('data-descripcion');
            const sucursal = this.getAttribute('data-sucursal');

            // Cambiar acción del form a PUT / update
            form.setAttribute('action', updateUrl);
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            // Rellenar campos
            inputNombre.value = nombre;
            inputDescripcion.value = descripcion !== 'null' ? descripcion : '';
            selectSucursal.value = sucursal;

            // Cambiar textos y estilos del panel derecho
            formTitleText.textContent = "Editar Tipo";
            formIcon.className = "bi bi-pencil-square text-warning fs-6";
            btnTextContent.textContent = "Actualizar Tipo";
            btnCancelEdit.classList.remove('d-none');

            // Bordes destacados visuales
            form.closest('.card').classList.add('border', 'border-warning');

            // Scroll suave hacia el formulario en la parte derecha/superior
            form.closest('.card').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Manejar clic en botón cancelar edición
    if (btnCancelEdit) {
        btnCancelEdit.addEventListener('click', function() {
            form.setAttribute('action', storeRoute);
            methodContainer.innerHTML = '';

            inputNombre.value = '';
            inputDescripcion.value = '';
            selectSucursal.value = '';

            formTitleText.textContent = "Nuevo Tipo";
            formIcon.className = "bi bi-plus-circle text-teal fs-6";
            btnTextContent.textContent = "Registrar Tipo";
            btnCancelEdit.classList.add('d-none');

            form.closest('.card').classList.remove('border', 'border-warning');
        });
    }

    // Validación interactiva al enviar
    if (form) {
        form.addEventListener('submit', function(e) {
            let valido = true;
            const regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;

            if (!inputNombre.value.trim() || !regexLetras.test(inputNombre.value.trim())) {
                inputNombre.classList.add('is-invalid');
                valido = false;
            } else {
                inputNombre.classList.remove('is-invalid');
                inputNombre.classList.add('is-valid');
            }

            if (inputDescripcion.value.trim() && !regexLetras.test(inputDescripcion.value.trim())) {
                inputDescripcion.classList.add('is-invalid');
                valido = false;
            } else {
                inputDescripcion.classList.remove('is-invalid');
            }

            if (!selectSucursal.value) {
                selectSucursal.classList.add('is-invalid');
                valido = false;
            } else {
                selectSucursal.classList.remove('is-invalid');
                selectSucursal.classList.add('is-valid');
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection