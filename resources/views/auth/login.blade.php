@extends('layouts.guest')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="row shadow-lg rounded-4 overflow-hidden w-100 bg-white login-main-card" style="max-width: 950px;">

        <!-- Panel izquierdo: Marca, Logo e Info (Ancho y estructura fija) -->
        <div class="col-lg-5 bg-ink p-4 p-md-5 d-flex flex-column justify-content-between text-white position-relative overflow-hidden left-brand-panel" style="background-color: #151515;">
            <!-- Efecto decorativo brillante con interacción JS -->
            <div class="brand-glow-circle"></div>

            <div class="position-relative z-1">
                <!-- LOGO REAL DESDE PUBLIC/LOGOS/CORTEBARBA.PNG -->
                <div class="mb-4 brand-logo-wrapper">
                    <img src="{{ asset('logos/cortebarba.png') }}" alt="Fede looK" class="img-fluid mb-2" style="max-height: 70px; object-fit: contain;">
                    <span class="d-block text-uppercase fw-bold text-teal" style="font-size: 0.75rem; letter-spacing: 0.2em; color: #00a99d;">Fede looK</span>
                </div>

                <h2 class="fw-bold text-uppercase mb-2" style="font-family: 'Bebas Neue', sans-serif; font-size: 2.5rem; color:#F6F2E8; letter-spacing: 1px; line-height: 1;">
                    Acceso Clientes
                </h2>
                <p class="mb-3 fw-semibold" style="color:#00a99d;">Tu lugar en la agenda.</p>
                <p class="small text-white-50 leading-relaxed">
                    Con tu cuenta accedés a precios, reservás turno con tu barbero de confianza y ves el historial de todos tus servicios.
                </p>
            </div>

            <div class="position-relative z-1 mt-4 pt-3 border-top border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2 text-white-50 small">
                    <i class="bi bi-shield-check text-teal fs-5" style="color: #00a99d;"></i>
                    <span>Plataforma oficial y segura</span>
                </div>
            </div>
        </div>

        <!-- Panel derecho: Tabs visibles y Formularios sin deformación -->
        <div class="col-lg-7 p-4 p-md-5 bg-light d-flex flex-column justify-content-center">
            
            <!-- Pestañas (Tabs) con visibilidad clara y colores contrastados -->
            <ul class="nav nav-pills nav-fill mb-4 bg-white p-1 rounded-pill shadow-sm border" id="loginTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill fw-bold py-2 transition-all" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab">Iniciar sesión</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill fw-bold py-2 transition-all" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab">Crear cuenta</button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- Iniciar sesión -->
                <div class="tab-pane fade show active" id="login" role="tabpanel">
                    <form method="POST" action="{{ route('login') }}" id="formLogin" novalidate>
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CORREO ELECTRÓNICO</label>
                            <input id="emailLogin" type="email" name="email" class="form-control py-2 shadow-none @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus placeholder="correo@ejemplo.com">
                            <div class="invalid-feedback">Por favor ingresa un correo electrónico válido.</div>
                            @error('email') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CONTRASEÑA</label>
                            <div class="input-group shadow-sm rounded-2 overflow-hidden">
                                <input id="passwordLogin" type="password" name="password" class="form-control py-2 border-end-0 @error('password') is-invalid @enderror" required placeholder="Tu contraseña">
                                <button class="btn btn-outline-secondary border-start-0 px-3 bg-white text-muted" type="button" id="toggleLoginPassword">
                                    <i class="bi bi-eye" id="iconoOjitoLogin"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">La contraseña es obligatoria.</div>
                            @error('password') 
                                <div class="invalid-feedback d-block">{{ $message }}</div> 
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-teal w-100 fw-bold py-2 rounded-pill shadow-sm mt-2 text-white">Iniciar Sesión</button>
                        
                        <!-- Separador visual -->
                        <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1 text-muted opacity-25">
                            <span class="px-2 text-muted small fw-semibold">O</span>
                            <hr class="flex-grow-1 text-muted opacity-25">
                        </div>

                        <!-- Botón de Google -->
                        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100 py-2 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2 text-decoration-none fw-bold bg-white text-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                            </svg>
                            Continuar con Google
                        </a>

                        <div class="mt-3 text-center">
                            <a href="{{ route('password.request') }}" class="small text-muted text-decoration-none hover-teal">¿Olvidaste tu contraseña? Recuperarla</a>
                        </div>
                    </form>
                </div>

                <!-- Crear cuenta -->
                <div class="tab-pane fade" id="register" role="tabpanel">
                    <form method="POST" action="{{ route('register') }}" id="formRegister" novalidate>
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">NOMBRE COMPLETO</label>
                            <input type="text" name="nombre" id="nameRegister" class="form-control py-2 shadow-none @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Carlos Gómez" minlength="3">
                            <div class="invalid-feedback">El nombre debe tener al menos 3 caracteres y solo letras.</div>
                            @error('nombre') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CORREO ELECTRÓNICO</label>
                            <input type="email" name="email" id="emailRegister" class="form-control py-2 shadow-none @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="correo@ejemplo.com">
                            <div class="invalid-feedback">Por favor ingresa un correo electrónico válido.</div>
                            @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">TELÉFONO / WHATSAPP <span class="text-muted fw-normal lowercase">(Solo números)</span></label>
                            <input type="tel" name="telefono" id="telefonoRegister" class="form-control py-2 shadow-none @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="Ej. 3881234567" maxlength="15">
                            <div class="invalid-feedback">Ingresa un número telefónico válido (mínimo 7 dígitos).</div>
                            @error('telefono') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CONTRASEÑA</label>
                            <div class="input-group shadow-sm rounded-2 overflow-hidden">
                                <input type="password" name="password" id="passwordRegister" class="form-control py-2 border-end-0 @error('password') is-invalid @enderror" required minlength="8" placeholder="Mínimo 8 caracteres">
                                <button class="btn btn-outline-secondary border-start-0 px-3 bg-white text-muted" type="button" id="toggleRegisterPassword">
                                    <i class="bi bi-eye" id="iconoOjitoRegister"></i>
                                </button>
                            </div>

                            <!-- Panel de Requisitos de Contraseña en Vivo -->
                            <div class="p-3 bg-white rounded-3 mt-2 border small shadow-sm" id="passwordChecklist">
                                <div class="text-muted fw-bold mb-2" style="font-size: 0.70rem; letter-spacing: 0.5px;">REQUISITOS DE INTEGRIDAD DE LA CONTRASEÑA:</div>
                                <div class="row g-1">
                                    <div class="col-md-6 text-danger" id="req-length"><i class="bi bi-x-circle me-1"></i> Mínimo 8 caracteres</div>
                                    <div class="col-md-6 text-danger" id="req-lower"><i class="bi bi-x-circle me-1"></i> Una letra minúscula</div>
                                    <div class="col-md-6 text-danger" id="req-upper"><i class="bi bi-x-circle me-1"></i> Una letra mayúscula</div>
                                    <div class="col-md-6 text-danger" id="req-number"><i class="bi bi-x-circle me-1"></i> Un número</div>
                                    <div class="col-12 text-danger" id="req-symbol"><i class="bi bi-x-circle me-1"></i> Un símbolo especial (. - _ $ # @ ! etc.)</div>
                                </div>
                            </div>
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>

                        <button type="submit" class="btn btn-dark w-100 fw-bold py-2 rounded-pill shadow-sm mt-2">Crear Cuenta</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* Corrección de la tarjeta y rigidez de columnas */
.login-main-card {
    border: 1px solid rgba(0,0,0,0.06);
}

.left-brand-panel {
    min-height: 100%;
}

/* Efecto de luz dinámico con el puntero */
.brand-glow-circle {
    position: absolute;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(0,169,157,0.3) 0%, rgba(21,21,21,0) 70%);
    top: -50px;
    left: -50px;
    border-radius: 50%;
    pointer-events: none;
    transition: transform 0.15s ease-out;
}

/* Estilizado estricto para las solapas (Tabs) activas e inactivas */
.nav-pills .nav-link {
    color: #495057 !important; /* Gris oscuro visible cuando no está seleccionada */
    background-color: transparent;
    transition: all 0.25s ease;
}

.nav-pills .nav-link.active {
    background-color: #151515 !important;
    color: #ffffff !important; /* Texto blanco en el botón activo */
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
}

/* Botones institucionales en degrade Teal */
.btn-teal {
    background: linear-gradient(120deg, #00a878 0%, #00a99d 52%, #16849f 100%);
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-teal:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 169, 157, 0.35) !important;
}

.hover-teal:hover {
    color: #00a99d !important;
}

/* Evitar resaltados extraños al hacer focus en los inputs */
.form-control:focus {
    border-color: #00a99d;
    box-shadow: 0 0 0 0.2rem rgba(0, 169, 157, 0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // A. Movimiento sutil de luz interactiva en el panel izquierdo
    const leftPanel = document.querySelector('.left-brand-panel');
    const glowCircle = document.querySelector('.brand-glow-circle');

    if (leftPanel && glowCircle) {
        leftPanel.addEventListener('mousemove', function(e) {
            const rect = leftPanel.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            glowCircle.style.transform = `translate(${x - 110}px, ${y - 110}px)`;
        });
    }

    // 1. Ojito para Login
    const toggleLoginBtn = document.getElementById('toggleLoginPassword');
    const inputLoginPass = document.getElementById('passwordLogin');
    const iconoOjitoLogin = document.getElementById('iconoOjitoLogin');

    if (toggleLoginBtn && inputLoginPass) {
        toggleLoginBtn.addEventListener('click', function() {
            const type = inputLoginPass.getAttribute('type') === 'password' ? 'text' : 'password';
            inputLoginPass.setAttribute('type', type);
            if (iconoOjitoLogin) {
                iconoOjitoLogin.className = type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    }

    // 2. Ojito para Registro
    const toggleRegisterBtn = document.getElementById('toggleRegisterPassword');
    const inputRegisterPass = document.getElementById('passwordRegister');
    const iconoOjitoRegister = document.getElementById('iconoOjitoRegister');

    if (toggleRegisterBtn && inputRegisterPass) {
        toggleRegisterBtn.addEventListener('click', function() {
            const type = inputRegisterPass.getAttribute('type') === 'password' ? 'text' : 'password';
            inputRegisterPass.setAttribute('type', type);
            if (iconoOjitoRegister) {
                iconoOjitoRegister.className = type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    }

    // 3. Validaciones en vivo para Nombre en Registro
    const inputNameReg = document.getElementById('nameRegister');
    if (inputNameReg) {
        inputNameReg.addEventListener('input', function() {
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

    // 4. Validaciones en vivo para Email en Registro
    const inputEmailReg = document.getElementById('emailRegister');
    if (inputEmailReg) {
        inputEmailReg.addEventListener('input', function() {
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

    // 5. Validación en vivo del Teléfono (Solo Números y longitud mínima)
    const inputTelefonoReg = document.getElementById('telefonoRegister');
    if (inputTelefonoReg) {
        inputTelefonoReg.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length >= 7) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value.length > 0) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // 6. Checklist Dinámico en Registro para la Contraseña
    const inputPasswordReg = document.getElementById('passwordRegister');
    if (inputPasswordReg) {
        inputPasswordReg.addEventListener('input', function() {
            const val = this.value;

            const hasLength = val.length >= 8;
            const hasLower = /[a-z]/.test(val);
            const hasUpper = /[A-Z]/.test(val);
            const hasNumber = /\d/.test(val);
            const hasSymbol = /[.,\-_$#@!*&+=?]/.test(val);

            function actualizarReq(id, cumple, texto) {
                const el = document.getElementById(id);
                if (!el) return;
                if (cumple) {
                    el.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + texto;
                    el.className = 'col-md-6 text-success fw-semibold';
                } else {
                    el.innerHTML = '<i class="bi bi-x-circle me-1"></i> ' + texto;
                    el.className = 'col-md-6 text-danger';
                }
            }

            actualizarReq('req-length', hasLength, 'Mínimo 8 caracteres');
            actualizarReq('req-lower', hasLower, 'Una letra minúscula');
            actualizarReq('req-upper', hasUpper, 'Una letra mayúscula');
            actualizarReq('req-number', hasNumber, 'Un número');
            actualizarReq('req-symbol', hasSymbol, 'Un símbolo especial (. - _ $ # @ ! etc.)');

            const passValida = hasLength && hasLower && hasUpper && hasNumber && hasSymbol;
            if (passValida) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (val.length > 0) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }
});
</script>
@endsection