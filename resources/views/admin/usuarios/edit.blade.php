@extends('layouts.app')

@section('content')
<div class="container py-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            
            <!-- Encabezado de la Sección -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary btn-sm me-3 rounded-circle shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h2 class="fw-bold text-dark mb-0">Editar Cliente</h2>
                    <p class="text-muted small mb-0">Modifica los datos personales o restablece la contraseña del cliente.</p>
                </div>
            </div>

            <!-- Tarjeta del Formulario -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 bg-white">

                    <!-- Manejo de Errores del Backend -->
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                            <div>
                                <span class="fw-bold d-block">Por favor corrige los siguientes errores:</span>
                                <ul class="mb-0 ps-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" id="formEditarUsuario" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Nombre y Apellido (100% Blindado contra números) -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-semibold text-secondary small">NOMBRE Y APELLIDO</label>
                            <input type="text" name="nombre" id="nombre" class="form-control py-2" value="{{ old('nombre', $usuario->nombre) }}" placeholder="Ej. Carlos Gómez" required minlength="3" autocomplete="off">
                            <div class="invalid-feedback">El nombre y apellido debe tener al menos 3 caracteres y solo debe contener letras.</div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-secondary small">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" id="email" class="form-control py-2" value="{{ old('email', $usuario->email) }}" placeholder="correo@ejemplo.com" required autocomplete="off">
                            <div class="invalid-feedback">Por favor ingresa un correo electrónico válido.</div>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-semibold text-secondary small">TELÉFONO <span class="text-muted fw-normal">(Solo números, opcional)</span></label>
                            <input type="tel" name="telefono" id="telefono" class="form-control py-2" value="{{ old('telefono', $usuario->telefono) }}" placeholder="Ej. 388..." maxlength="15" autocomplete="off">
                        </div>

                        <!-- Cambio/Reset de Contraseña con Ojito -->
                        <div class="mb-4">
                            <label for="inputPassword" class="form-label fw-semibold text-secondary small">NUEVA CONTRASEÑA <span class="text-muted fw-normal">(Opcional)</span></label>
                            <div class="input-group">
                                <input type="password" id="inputPassword" name="password" class="form-control py-2" placeholder="Dejar en blanco para mantener la actual" minlength="6" autocomplete="new-password">
                                <button class="btn btn-outline-secondary px-3" type="button" id="btnEye">
                                    <i class="bi bi-eye" id="iconoOjito"></i>
                                </button>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                Escribí una clave solo si necesitás restablecer la contraseña de este cliente (mínimo 6 caracteres).
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex align-items-center justify-content-end gap-3 pt-3 border-top">
                            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light text-secondary px-4 fw-semibold rounded-pill">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-5 fw-semibold rounded-pill shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">Actualizar Cliente</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputNombre = document.getElementById('nombre');
    const inputEmail = document.getElementById('email');
    const inputTelefono = document.getElementById('telefono');
    const inputPass = document.getElementById('inputPassword');
    const btnEye = document.getElementById('btnEye');
    const iconoOjito = document.getElementById('iconoOjito');
    const form = document.getElementById('formEditarUsuario');

    // 1. Ojito de Contraseña seguro
    if (btnEye && inputPass) {
        btnEye.addEventListener('click', function() {
            const type = inputPass.getAttribute('type') === 'password' ? 'text' : 'password';
            inputPass.setAttribute('type', type);
            if (iconoOjito) {
                iconoOjito.className = type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    }

    // 2. Teléfono: Solo números en tiempo real
    if (inputTelefono) {
        inputTelefono.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    // 3. Validación estricta 100% para Nombre y Apellido (Bloqueo de números por tecla y pegado)
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
            
            if (this.value.trim().length >= 3) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.trim().length > 0) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // 4. Validación en vivo del Email
    if (inputEmail) {
        inputEmail.addEventListener('input', function() {
            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (regexEmail.test(this.value.trim())) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.trim().length > 0) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // 5. Validación completa al hacer Submit
    if (form) {
        form.addEventListener('submit', function(e) {
            let valido = true;

            const regexLetras = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;
            if (inputNombre.value.trim().length < 3 || !regexLetras.test(inputNombre.value.trim())) {
                inputNombre.classList.add('is-invalid');
                valido = false;
            }

            const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regexEmail.test(inputEmail.value.trim())) {
                inputEmail.classList.add('is-invalid');
                valido = false;
            }

            if (!valido) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endsection