@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <!-- Encabezado -->
            <div class="d-flex align-items-center mb-4 pb-3 border-bottom animate-fade-in">
                <a href="{{ route('admin.servicios.index') }}" class="btn btn-outline-secondary btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center btn-hover-scale" style="width: 38px; height: 38px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h4 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <span class="p-1.5 text-white rounded-2 shadow-sm brand-icon-container" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                            <img src="{{ asset('logos/cortebarba2.png') }}" alt="Logo" class="logo-header-inline">
                        </span>
                        Crear Nuevo Servicio
                    </h1>
                    <p class="text-muted small mb-0">Configura la jerarquía, detalles comerciales y la multimedia del servicio.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center animate-fade-in" role="alert" style="background-color: #fef2f2; color: #991b1b;">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div>
                        <span class="fw-bold d-block small">Por favor corrige los siguientes errores:</span>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Tarjeta del Formulario Principal -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden card-modern">
                <div class="card-body p-4 bg-white">

                    <form action="{{ route('admin.servicios.store') }}" method="POST" enctype="multipart/form-data" id="formCrearServicio" novalidate>
                        @csrf

                        <!-- SECCIÓN 1: Ubicación y Categoría (Cascada) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge rounded-pill bg-teal-soft text-teal px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">1</span>
                                <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Ubicación y Categoría</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="sucursal_id" class="form-label fw-semibold text-secondary small">SUCURSAL <span class="text-danger">*</span></label>
                                    <select name="sucursal_id" id="sucursal_id" class="form-select border bg-light py-2" required>
                                        <option value="">Selecciona una sucursal...</option>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                                {{ $sucursal->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Selecciona una sucursal obligatoria.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="tipo_servicio_id" class="form-label fw-semibold text-secondary small">TIPO DE SERVICIO <span class="text-danger">*</span></label>
                                    <select name="tipo_servicio_id" id="tipo_servicio_id" class="form-select border bg-light py-2" required disabled>
                                        <option value="">Selecciona primero una sucursal...</option>
                                    </select>
                                    <div class="invalid-feedback">Selecciona un tipo de servicio válido.</div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: Información Principal (Texto y Numéricos Estrictos) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge rounded-pill bg-teal-soft text-teal px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">2</span>
                                <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Información Principal y Comercial</h6>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="nombre" class="form-label fw-semibold text-secondary small">NOMBRE DEL SERVICIO <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control border bg-light py-2" placeholder="Ej: Corte Degradado Estilo" required minlength="3" autocomplete="off" disabled>
                                    <div class="invalid-feedback">El nombre del servicio solo debe contener letras y espacios (mínimo 3 caracteres).</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="precio_actual" class="form-label fw-semibold text-secondary small">PRECIO ($) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">$</span>
                                        <input type="number" step="0.01" min="0" name="precio_actual" id="precio_actual" value="{{ old('precio_actual') }}" class="form-control border-start-0 bg-light py-2" placeholder="0.00" required disabled>
                                    </div>
                                    <div class="invalid-feedback">Ingresa un precio numérico válido mayor o igual a 0.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="duracion" class="form-label fw-semibold text-secondary small">DURACIÓN (MINUTOS) <span class="text-danger">*</span></label>
                                    <input type="number" step="1" min="1" name="duracion" id="duracion" value="{{ old('duracion') }}" class="form-control border bg-light py-2" placeholder="Ej: 30" required disabled>
                                    <div class="invalid-feedback">Ingresa una duración válida en minutos (mínimo 1).</div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: Multimedia e Imagen (Exclusivamente Archivo Local) -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge rounded-pill bg-teal-soft text-teal px-2.5 py-1 fw-bold" style="font-size: 0.7rem;">3</span>
                                <h6 class="text-uppercase text-dark fw-bold mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Multimedia e Imagen del Servicio</h6>
                            </div>

                            <div class="bg-light p-3 rounded-3 border-0">
                                <label class="form-label fw-semibold text-secondary small d-block mb-2">IMAGEN DEL SERVICIO</label>

                                <!-- Zona de Arrastre para Archivo Local -->
                                <div id="bloque_local" class="mb-2">
                                    <div id="drop-zone" class="border border-2 border-dashed rounded-3 p-4 text-center position-relative bg-white drop-zone-hover shadow-xs" style="cursor: pointer; border-color: #cbd5e1 !important; transition: all 0.3s ease;">
                                        <input type="file" name="imagen" id="imagen" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;" accept="image/*" disabled>
                                        
                                        <div id="drop-zone-content" class="py-2">
                                            <div class="mb-2 text-teal">
                                                <i class="bi bi-cloud-arrow-up display-6"></i>
                                            </div>
                                            <h6 class="mb-1 fw-bold text-dark small">Arrastrá y soltá tu imagen acá</h6>
                                            <p class="mb-2 text-muted small">o <span class="text-teal fw-semibold">hacé clic para seleccionarla</span></p>
                                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">PNG, JPG, WEBP (Máx. 2MB)</span>
                                        </div>

                                        <!-- Vista previa ajustada -->
                                        <div id="preview-container" class="d-none animate-fade-in py-1">
                                            <div class="position-relative d-inline-block shadow-sm rounded-3 overflow-hidden bg-light border p-1 mb-1">
                                                <img id="image-preview" src="#" alt="Vista previa" class="rounded-2 mx-auto d-block" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                                                <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-success shadow-xs m-1" style="font-size: 0.5rem;">
                                                    <i class="bi bi-check-lg"></i>
                                                </span>
                                            </div>
                                            <p id="file-name" class="text-dark fw-bold small mb-1 text-truncate px-2"></p>
                                            <button type="button" id="remove-file" class="btn btn-sm btn-outline-danger py-0 px-2 mt-1" style="font-size: 0.7rem;">Quitar imagen</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.servicios.index') }}" class="btn btn-light text-secondary px-3 py-1.5 small fw-semibold rounded-pill btn-hover-scale">Cancelar</a>
                            <button type="submit" class="btn px-4 py-1.5 small font-weight-bold shadow-sm d-inline-flex align-items-center gap-2 btn-video-glow rounded-pill text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;" id="btn-submit" disabled>
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.854 1.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8.293 7.793l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                    <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2zm12 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12z"/>
                                </svg>
                                Crear Servicio
                            </button>
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
    .btn-hover-scale {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .btn-hover-scale:hover {
        transform: translateY(-1px);
        background-color: #e2e8f0 !important;
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.4);
    }
    .drop-zone-hover:hover {
        border-color: #0d9488 !important;
        background-color: #f0fdfa !important;
    }
    .text-teal {
        color: #0d9488 !important;
    }
    .bg-teal-soft {
        background-color: #f0fdfa !important;
    }
    .border-teal-subtle {
        border-color: #ccfbf1 !important;
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
    const sucursalSel = document.getElementById('sucursal_id');
    const tipoSel     = document.getElementById('tipo_servicio_id');
    const inputNombre = document.getElementById('nombre');
    
    const camposABilitar = ['nombre', 'duracion', 'precio_actual', 'imagen', 'btn-submit'];
    
    const inputImagen = document.getElementById('imagen');
    const dropZone = document.getElementById('drop-zone');
    const dropZoneContent = document.getElementById('drop-zone-content');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');
    const fileNameSpan = document.getElementById('file-name');
    const removeFileBtn = document.getElementById('remove-file');

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

    const tipos = @json($tipos);

    // Blindaje estricto 100% para el nombre (Solo letras y espacios)
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

    // 1. Cascada Sucursal -> Tipos de Servicio
    sucursalSel.addEventListener('change', function() {
        let sucursalId = this.value;
        tipoSel.innerHTML = '<option value="">Seleccione un tipo...</option>';
        tipoSel.disabled = true;

        camposABilitar.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.disabled = true;
        });
        
        resetearImagenLocal();

        if (sucursalId) {
            tipos.filter(t => t.sucursal_id == sucursalId).forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id;
                opt.textContent = t.nombre;
                tipoSel.appendChild(opt);
            });
            tipoSel.disabled = false;
        }
    });

    // 2. Habilitar formulario al seleccionar Tipo de Servicio
    tipoSel.addEventListener('change', function() {
        const habilitar = !this.value;
        camposABilitar.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.disabled = habilitar;
        });
    });

    // 3. Vista previa de imagen
    function mostrarVistaPrevia(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                fileNameSpan.textContent = file.name;
                dropZoneContent.classList.add('d-none');
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }
    }

    function resetearImagenLocal() {
        inputImagen.value = '';
        imagePreview.src = '#';
        fileNameSpan.textContent = '';
        previewContainer.classList.add('d-none');
        dropZoneContent.classList.remove('d-none');
    }

    inputImagen.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            mostrarVistaPrevia(this.files[0]);
        }
    });

    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetearImagenLocal();
        });
    }

    // Drag and Drop
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#0d9488';
            dropZone.style.backgroundColor = '#f0fdfa';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            dropZone.style.borderColor = '#cbd5e1';
            dropZone.style.backgroundColor = '#ffffff';
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0 && !inputImagen.disabled) {
            inputImagen.files = files;
            mostrarVistaPrevia(files[0]);
        }
    });

    // 4. Validaciones estrictas antes de enviar
    const form = document.getElementById('formCrearServicio');
    if (form) {
        form.addEventListener('submit', function(e) {
            const inputPrecio = document.getElementById('precio_actual');
            const inputDuracion = document.getElementById('duracion');
            let valido = true;
            const regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;

            if (!inputNombre.value.trim() || inputNombre.value.trim().length < 3 || !regexLetras.test(inputNombre.value.trim())) {
                inputNombre.classList.add('is-invalid');
                valido = false;
            } else {
                inputNombre.classList.remove('is-invalid');
                inputNombre.classList.add('is-valid');
            }

            if (inputPrecio.value === '' || isNaN(inputPrecio.value) || Number(inputPrecio.value) < 0) {
                inputPrecio.classList.add('is-invalid');
                valido = false;
            } else {
                inputPrecio.classList.remove('is-invalid');
                inputPrecio.classList.add('is-valid');
            }

            if (inputDuracion.value === '' || isNaN(inputDuracion.value) || Number(inputDuracion.value) < 1) {
                inputDuracion.classList.add('is-invalid');
                valido = false;
            } else {
                inputDuracion.classList.remove('is-invalid');
                inputDuracion.classList.add('is-valid');
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection