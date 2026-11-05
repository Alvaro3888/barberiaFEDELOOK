@extends('layouts.guest')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="row shadow-lg rounded-4 overflow-hidden w-100 bg-white login-main-card" style="max-width: 950px;">

        <!-- Panel izquierdo: Marca, Logo e Info -->
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
                    Nueva Clave
                </h2>
                <p class="mb-3 fw-semibold" style="color:#00a99d;">Seguridad para tu cuenta.</p>
                <p class="small text-white-50 leading-relaxed">
                    Ingresá tu nueva contraseña cumpliendo con los requisitos de seguridad para volver a acceder a la plataforma.
                </p>
            </div>

            <div class="position-relative z-1 mt-4 pt-3 border-top border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2 text-white-50 small">
                    <i class="bi bi-shield-check text-teal fs-5" style="color: #00a99d;"></i>
                    <span>Plataforma oficial y segura</span>
                </div>
            </div>
        </div>

        <!-- Panel derecho: Formulario de Restablecimiento con Validación en Vivo -->
        <div class="col-lg-7 p-4 p-md-5 bg-light d-flex flex-column justify-content-center">
            
            <div class="mb-4">
                <h3 class="fw-bold text-dark">Restablecer Contraseña</h3>
                <p class="text-muted small">Por favor, define tu nueva clave de acceso.</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" id="formResetPassword" novalidate>
                @csrf

                <!-- Token oculto -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Correo Electrónico -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CORREO ELECTRÓNICO</label>
                    <input id="email" type="email" name="email" class="form-control py-2 shadow-none @error('email') is-invalid @enderror" value="{{ old('email', $request->email) }}" required autofocus placeholder="correo@ejemplo.com">
                    @error('email') 
                        <div class="invalid-feedback d-block">{{ $message }}</div> 
                    @enderror
                </div>

                <!-- Nueva Contraseña -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">NUEVA CONTRASEÑA</label>
                    <div class="input-group shadow-sm rounded-2 overflow-hidden">
                        <input type="password" name="password" id="passwordRegister" class="form-control py-2 border-end-0 @error('password') is-invalid @enderror" required minlength="8" placeholder="Mínimo 8 caracteres">
                        <button class="btn btn-outline-secondary border-start-0 px-3 bg-white text-muted" type="button" id="toggleRegisterPassword">
                            <i class="bi bi-eye" id="iconoOjitoRegister"></i>
                        </button>
                    </div>

                    <!-- Panel de Requisitos de Contraseña en Vivo (Idéntico al Login) -->
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

                <!-- Confirmar Contraseña -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">CONFIRMAR CONTRASEÑA</label>
                    <div class="input-group shadow-sm rounded-2 overflow-hidden">
                        <input type="password" name="password_confirmation" id="passwordConfirmation" class="form-control py-2 border-end-0 @error('password_confirmation') is-invalid @enderror" required placeholder="Repite tu contraseña">
                        <button class="btn btn-outline-secondary border-start-0 px-3 bg-white text-muted" type="button" id="toggleConfirmPassword">
                            <i class="bi bi-eye" id="iconoOjitoConfirm"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirmFeedback">Las contraseñas deben coincidir.</div>
                    @error('password_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-teal w-100 fw-bold py-2 rounded-pill shadow-sm mt-2 text-white">Guardar Nueva Contraseña</button>
            </form>
        </div>

    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
.login-main-card {
    border: 1px solid rgba(0,0,0,0.06);
}

.left-brand-panel {
    min-height: 100%;
}

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

.btn-teal {
    background: linear-gradient(120deg, #00a878 0%, #00a99d 52%, #16849f 100%);
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.btn-teal:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 169, 157, 0.35) !important;
}

.form-control:focus {
    border-color: #00a99d;
    box-shadow: 0 0 0 0.2rem rgba(0, 169, 157, 0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Movimiento sutil de luz interactiva en el panel izquierdo
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

    // 2. Ojito para Nueva Contraseña
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

    // 3. Ojito para Confirmar Contraseña
    const toggleConfirmBtn = document.getElementById('toggleConfirmPassword');
    const inputConfirmPass = document.getElementById('passwordConfirmation');
    const iconoOjitoConfirm = document.getElementById('iconoOjitoConfirm');

    if (toggleConfirmBtn && inputConfirmPass) {
        toggleConfirmBtn.addEventListener('click', function() {
            const type = inputConfirmPass.getAttribute('type') === 'password' ? 'text' : 'password';
            inputConfirmPass.setAttribute('type', type);
            if (iconoOjitoConfirm) {
                iconoOjitoConfirm.className = type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    }

    // 4. Checklist Dinámico de Contraseña (Idéntico al del registro)
    const inputPasswordReg = document.getElementById('passwordRegister');
    const inputConfirmPassEl = document.getElementById('passwordConfirmation');

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

            // Validar coincidencia en tiempo real si ya escribió en confirmación
            if (inputConfirmPassEl && inputConfirmPassEl.value.length > 0) {
                if (inputConfirmPassEl.value === val) {
                    inputConfirmPassEl.classList.remove('is-invalid');
                    inputConfirmPassEl.classList.add('is-valid');
                } else {
                    inputConfirmPassEl.classList.add('is-invalid');
                    inputConfirmPassEl.classList.remove('is-valid');
                }
            }
        });
    }

    // 5. Validación en vivo de Confirmación de Contraseña
    if (inputConfirmPassEl && inputPasswordReg) {
        inputConfirmPassEl.addEventListener('input', function() {
            if (this.value === inputPasswordReg.value && this.value.length > 0) {
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
});
</script>
@endsection