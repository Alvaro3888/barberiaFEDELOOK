@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <!-- Tarjeta Principal con Diseño Minimalista y Limpio -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden card-animated-entry">
                
                <!-- Encabezado Limpio -->
                <div class="card-header py-4 px-4 px-md-5 border-0 text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <h3 class="mb-1 font-weight-bold text-white">Registrar Nuevo Miembro</h3>
                    <p class="mb-0 text-white-50 small">Incorpora personal operativo al sistema (Administrador o Barbero).</p>
                </div>

                <!-- Cuerpo del Formulario -->
                <div class="card-body p-4 p-md-5 bg-white">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4 shadow-xs mb-4 p-3 bg-danger-subtle text-danger" role="alert">
                            <div class="d-flex align-items-center gap-2 mb-1 font-weight-bold">
                                <span>Por favor corrige los siguientes errores:</span>
                            </div>
                            <ul class="mb-0 small ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.personal.store') }}" method="POST" id="formPersonalCreate" class="needs-validation" novalidate>
                        @csrf

                        <!-- Nombre Completo (Solo Letras) -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="inputNombre" class="form-control form-control-lg border bg-light py-2 text-dark font-weight-medium fs-6 input-solo-letras" value="{{ old('nombre') }}" required placeholder="Ej. Carlos Gómez" autocomplete="off">
                            <div class="invalid-feedback small">El nombre solo debe contener letras y espacios.</div>
                        </div>

                        <!-- Fila: Correo y Teléfono -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg border bg-light py-2 text-dark font-weight-medium fs-6" value="{{ old('email') }}" required placeholder="correo@ejemplo.com">
                                <div class="invalid-feedback small">Ingresa un correo electrónico válido.</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Teléfono (Opcional)</label>
                                <input type="text" name="telefono" id="inputTelefono" class="form-control form-control-lg border bg-light py-2 text-dark font-weight-medium fs-6 input-solo-numeros" value="{{ old('telefono') }}" placeholder="Ej. 3888123456" autocomplete="off">
                                <div class="invalid-feedback small">El teléfono solo puede contener números, espacios, guiones o el signo +.</div>
                            </div>
                        </div>

                        <!-- Fila: Rol y Contraseña -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Rol Operativo <span class="text-danger">*</span></label>
                                <select name="rol_id" class="form-select form-select-lg border bg-light py-2 text-dark font-weight-medium fs-6 shadow-none" required>
                                    <option value="">Selecciona el rol...</option>
                                    @foreach($rolesPersonal as $rol)
                                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                            {{ ucfirst($rol->nombre) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback small">Selecciona un rol válido.</div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Contraseña Temporal <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="inputPassword" class="form-control form-control-lg border bg-light py-2 text-dark font-weight-medium fs-6" required placeholder="Mínimo 6 caracteres">
                                <div class="progress mt-2 rounded-pill" style="height: 4px;">
                                    <div id="passwordStrengthBar" class="progress-bar bg-danger" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="passwordFeedback" class="text-muted" style="font-size: 0.7rem;">Debe tener al menos 6 caracteres.</small>
                            </div>
                        </div>

                        <!-- Fila: Sucursal y Turno -->
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Sucursal Asignada</label>
                                <select name="sucursal_id" class="form-select form-select-lg border bg-light py-2 text-dark font-weight-medium fs-6 shadow-none">
                                    <option value="">Sin sucursal fija</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                                            {{ $sucursal->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label font-weight-bold text-dark small text-uppercase tracking-wider">Franja Horaria / Turno</label>
                                <select name="turno_trabajo_id" class="form-select form-select-lg border bg-light py-2 text-dark font-weight-medium fs-6 shadow-none">
                                    <option value="">Sin turno fijo</option>
                                    @foreach($turnos as $turno)
                                        <option value="{{ $turno->id }}" {{ old('turno_trabajo_id') == $turno->id ? 'selected' : '' }}>
                                            {{ $turno->nombre }} ({{ substr($turno->hora_inicio, 0, 5) }} - {{ substr($turno->hora_fin, 0, 5) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-3 border-top">
                            <a href="{{ route('admin.personal.index') }}" class="btn btn-light px-4 py-3 font-weight-bold text-secondary border shadow-xs btn-hover-scale rounded-3">
                                Cancelar
                            </a>
                            <button type="submit" id="btnSubmit" class="btn px-5 py-3 font-weight-bold shadow-sm btn-video-glow rounded-3 text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">
                                <span class="btn-text">Guardar Personal</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para Validaciones Estrictas y Medidor de Contraseña -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const passwordInput = document.getElementById('inputPassword');
        const progressBar = document.getElementById('passwordStrengthBar');
        const passwordFeedback = document.getElementById('passwordFeedback');
        const form = document.getElementById('formPersonalCreate');
        const btnSubmit = document.getElementById('btnSubmit');
        const inputNombre = document.getElementById('inputNombre');
        const inputTelefono = document.getElementById('inputTelefono');

        // 1. Blindaje estricto para Nombre (Solo letras y espacios)
        if (inputNombre) {
            inputNombre.addEventListener('keypress', function(e) {
                const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
                if (!/^[A-Za-zÀ-ÿ\s]$/.test(charStr)) {
                    e.preventDefault();
                }
            });

            inputNombre.addEventListener('paste', function(e) {
                e.preventDefault();
                const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
                const textoLimpio = textoPegado.replace(/[^A-Za-zÀ-ÿ\s]/g, '');
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
                this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
            });

            inputNombre.addEventListener('input', function() {
                if (this.value.trim().length === 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }

        // 2. Validación para Teléfono (Solo números, espacios, guiones y +)
        if (inputTelefono) {
            inputTelefono.addEventListener('keypress', function(e) {
                const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
                if (!/^[0-9\+\-\s]$/.test(charStr)) {
                    e.preventDefault();
                }
            });
        }

        // 3. Medidor de seguridad de contraseña
        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let strength = 0;

            if (val.length >= 6) strength += 40;
            if (val.length >= 10) strength += 30;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) strength += 30;

            progressBar.style.width = strength + '%';

            if (strength < 40) {
                progressBar.className = 'progress-bar bg-danger';
                passwordFeedback.textContent = 'Contraseña débil (mínimo 6 caracteres).';
            } else if (strength < 70) {
                progressBar.className = 'progress-bar bg-warning';
                passwordFeedback.textContent = 'Contraseña aceptable.';
            } else {
                progressBar.className = 'progress-bar bg-success';
                passwordFeedback.textContent = '¡Contraseña fuerte y segura!';
            }
        });

        // 4. Control final al enviar formulario
        form.addEventListener('submit', function(e) {
            const regexLetras = /^[A-Za-zÀ-ÿ\s]+$/;
            if (inputNombre && !regexLetras.test(inputNombre.value.trim())) {
                e.preventDefault();
                inputNombre.classList.add('is-invalid');
                inputNombre.focus();
                return;
            }

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Guardando en el sistema...
            `;
        });
    });
</script>

<style>
    .card-animated-entry {
        animation: fadeInScale 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.08) !important;
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: translateY(15px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .form-control, .form-select {
        transition: all 0.3s ease;
        border-color: #cbd5e1 !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0d9488 !important;
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12) !important;
    }

    .btn-hover-scale {
        transition: all 0.2s ease;
    }
    .btn-hover-scale:hover {
        transform: translateY(-2px);
        background-color: #f1f5f9 !important;
    }

    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.5);
    }
</style>
@endsection