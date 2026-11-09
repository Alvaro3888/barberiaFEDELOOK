@php
    $layout = 'layouts.public'; // Por defecto para clientes (rol 3) o invitados

    if (auth()->check()) {
        $rolId = (int) auth()->user()->rol_id;
        
        if ($rolId === 1) {
            $layout = 'layouts.app';      // Administrador
        } elseif ($rolId === 2) {
            $layout = 'layouts.barbero';  // Barbero
        } else {
            $layout = 'layouts.public';   // Cliente (rol 3)
        }
    }
@endphp

@extends($layout)

@section('content')
<!-- Contenedor optimizado para evitar saltos y padding excesivo en móviles -->
<div class="container py-4 py-md-5" style="max-width: 900px; min-height: 80vh;">
    
    <!-- ENCABEZADO DE SEGURIDAD Y PERFIL -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 p-md-4 bg-white rounded-4 shadow-sm border">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 48px; height: 48px; background: linear-gradient(135deg, #168F87 0%, #0F746E 100%); font-size: 20px;">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <div>
                        <h1 class="h5 fw-bold text-dark mb-1 fs-6 fs-md-5">Configuración de Seguridad y Perfil</h1>
                        <p class="text-muted small mb-0" style="font-size: 0.85rem;">Tus datos están protegidos. Gestioná tu identidad y accesos con total privacidad.</p>
                    </div>
                </div>
                <div class="badge bg-light text-secondary border px-3 py-2 rounded-pill small fw-semibold">
                    <i class="bi bi-check-circle-fill text-teal me-1"></i> Sesión Encriptada
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill fs-5 me-3"></i>
            <div><span class="fw-semibold">{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" role="alert" style="background-color: #fef2f2; color: #991b1b;">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <span class="fw-bold">Por favor corregí los siguientes errores de validación:</span>
            </div>
            <ul class="mb-0 ps-4 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        
        <!-- COLUMNA IZQUIERDA: IDENTIDAD Y FOTO -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white text-center p-3 p-md-4">
                <div class="card-body d-flex flex-column align-items-center justify-content-center py-2">
                    
                    <div class="position-relative mb-3">
                        @php
                            $avatar = $user->avatar ?? null;
                            $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                        @endphp

                        <div id="avatar-container-img">
                            @if($avatar)
                                @if($isGoogleAvatar)
                                    <img id="avatar-preview" src="{{ $avatar }}" alt="Avatar" class="rounded-circle shadow border border-3 border-white object-fit-cover" style="width: 110px; height: 110px;">
                                @else
                                    <img id="avatar-preview" src="{{ asset('storage/' . $avatar) }}" alt="Avatar" class="rounded-circle shadow border border-3 border-white object-fit-cover" style="width: 110px; height: 110px;">
                                @endif
                                <div id="avatar-placeholder" class="rounded-circle shadow border border-3 border-white d-none align-items-center justify-content-center text-teal mx-auto bg-light" style="width: 110px; height: 110px;">
                                    <i class="bi bi-person-fill" style="font-size: 45px;"></i>
                                </div>
                            @else
                                <div id="avatar-placeholder" class="rounded-circle shadow border border-3 border-white d-flex align-items-center justify-content-center text-teal mx-auto bg-light" style="width: 110px; height: 110px;">
                                    <i class="bi bi-person-fill" style="font-size: 45px;"></i>
                                </div>
                                <img id="avatar-preview" src="#" alt="Vista previa" class="rounded-circle shadow border border-3 border-white object-fit-cover d-none" style="width: 110px; height: 110px;">
                            @endif
                        </div>

                        <!-- Botón flotante de cámara -->
                        <label for="avatar-input" class="position-absolute bottom-0 end-0 text-white rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #168F87; cursor: pointer;" title="Cambiar foto">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>

                    <!-- Input file invisible pero funcional -->
                    <input type="file" id="avatar-input" class="d-none" accept="image/*">

                    <h5 class="fw-bold text-dark mb-1 text-truncate w-100 fs-6">{{ $user->name ?? $user->nombre ?? 'Usuario' }}</h5>
                    <p class="text-muted small mb-3 text-truncate w-100" style="font-size: 0.8rem;">{{ $user->email }}</p>

                    <div class="w-100 d-grid gap-2">
                        <label for="avatar-input" class="btn btn-outline-teal btn-sm rounded-pill fw-semibold py-2" style="cursor: pointer; font-size: 0.85rem;">
                            <i class="bi bi-upload me-1"></i> Cambiar Fotografía
                        </label>

                        <button type="button" id="btn-remover-avatar" class="btn btn-light text-danger btn-sm rounded-pill fw-semibold py-1.5 border-0 small w-100 {{ !$avatar ? 'd-none' : '' }}" style="font-size: 0.8rem;">
                            <i class="bi bi-trash me-1"></i> Remover foto actual
                        </button>
                    </div>

                    <div class="form-text text-muted mt-2" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success"></i> Sincronizado de forma segura.
                    </div>

                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: DATOS PERSONALES -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4 h-100">
                <div class="card-body">
                    
                    <form action="{{ route('perfil.update') }}" method="POST" id="formPerfil">
                        @csrf
                        @method('PUT')

                        <h5 class="fw-bold text-dark mb-3 fs-6 border-bottom pb-2">
                            <i class="bi bi-person-badge text-teal me-2"></i> Información Personal
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="nombre" class="form-label fw-semibold text-secondary small">NOMBRE Y APELLIDO <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nombre" id="nombre" class="form-control bg-light border-start-0 py-2.5 fs-6" value="{{ old('nombre', $user->name ?? $user->nombre ?? '') }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="email" class="form-label fw-semibold text-secondary small">CORREO ELECTRÓNICO (PRINCIPAL) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control bg-light border-start-0 py-2.5 fs-6" value="{{ old('email', $user->email) }}" required>
                                </div>
                                <div class="form-text small text-muted">Este correo se utiliza para tus accesos y notificaciones de turnos.</div>
                            </div>

                            <div class="col-12">
                                <label for="telefono" class="form-label fw-semibold text-secondary small">TELÉFONO / WHATSAPP DE CONTACTO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-whatsapp"></i></span>
                                    <input type="text" name="telefono" id="telefono" class="form-control bg-light border-start-0 py-2.5 fs-6" value="{{ old('telefono', $user->telefono ?? '') }}" placeholder="Ej: 3884123456">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn text-white px-4 py-2.5 fw-semibold rounded-pill shadow-sm w-100 w-md-auto" style="background: #168F87;">
                                <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

    <!-- ZONA DE PELIGRO / PRIVACIDAD ESTRICTA -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white border-top border-danger border-4 p-3 p-md-4">
                <div class="card-body">
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="text-danger bg-light p-2.5 rounded-3">
                            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0 fs-6">Zona de Privacidad y Baja de Cuenta</h5>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Control total sobre tus datos personales en nuestros servidores.</p>
                        </div>
                    </div>

                    <p class="text-muted small mb-4">Si decidís dar de baja tu cuenta, eliminaremos permanentemente tu perfil y registros de turnos de la barbería de forma irreversible.</p>
                    
                    <form action="{{ route('perfil.destroy') }}" method="POST" onsubmit="return confirm('¿Estás 100% seguro de que deseas dar de baja tu cuenta y borrar tus datos de forma permanente?');" class="row g-3 align-items-center">
                        @csrf
                        @method('DELETE')

                        <div class="col-12 col-md-8">
                            <input type="password" name="password_confirm_delete" id="password_confirm_delete" class="form-control bg-light border py-2.5" placeholder="Ingresá tu contraseña actual para autorizar la baja..." required>
                        </div>

                        <div class="col-12 col-md-4 text-md-end">
                            <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold rounded-pill w-100 shadow-sm" style="font-size: 0.85rem;">
                                <i class="bi bi-trash3 me-1"></i> Dar de baja mi cuenta
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .text-teal {
        color: #168F87 !important;
    }
    .btn-outline-teal {
        color: #168F87;
        border-color: #168F87;
        background: transparent;
        transition: all 0.2s ease;
    }
    .btn-outline-teal:hover {
        color: #fff;
        background-color: #168F87;
        border-color: #168F87;
    }
    .form-control:focus {
        border-color: #168F87 !important;
        box-shadow: 0 0 0 3px rgba(22, 143, 135, 0.15) !important;
        background-color: #fff !important;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarPlaceholder = document.getElementById('avatar-placeholder');
    const btnRemover = document.getElementById('btn-remover-avatar');
    
    // Elementos globales del Navbar (Layouts)
    const globalAvatars = document.querySelectorAll('.global-user-avatar');
    const navbarPlaceholder = document.getElementById('navbar-avatar-placeholder');
    const navbarPreview = document.getElementById('navbar-avatar-preview');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

    // 1. Subir automáticamente por AJAX al seleccionar archivo
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const formData = new FormData();
                formData.append('avatar', this.files[0]);

                if (avatarPreview) avatarPreview.style.opacity = '0.5';

                fetch("{{ route('perfil.avatar.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        let mensajeError = data.message || 'Error desconocido';
                        if (data.errors && data.errors.avatar) {
                            mensajeError = data.errors.avatar[0];
                        }
                        throw new Error(mensajeError);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        // Actualizar vista de perfil local
                        if (avatarPlaceholder) avatarPlaceholder.classList.add('d-none');
                        if (avatarPreview) {
                            avatarPreview.src = data.url;
                            avatarPreview.classList.remove('d-none');
                            avatarPreview.style.opacity = '1';
                        }
                        if (btnRemover) btnRemover.classList.remove('d-none');

                        // Actualizar en el Navbar al instante
                        globalAvatars.forEach(img => {
                            img.src = data.url;
                            img.classList.remove('d-none');
                        });
                        if (navbarPlaceholder) {
                            navbarPlaceholder.classList.add('d-none');
                        }
                        if (navbarPreview) {
                            navbarPreview.src = data.url;
                            navbarPreview.classList.remove('d-none');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error detallado:', error);
                    alert('Error al subir la imagen: ' + error.message);
                    if (avatarPreview) avatarPreview.style.opacity = '1';
                });
            }
        });
    }

    // 2. Eliminar automáticamente por AJAX al hacer clic en remover
    if (btnRemover) {
        btnRemover.addEventListener('click', function() {
            if (confirm('¿Estás seguro de quitar tu foto de perfil?')) {
                fetch("{{ route('perfil.avatar.destroy') }}", {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Limpiar perfil local
                        if (avatarPreview) {
                            avatarPreview.src = '#';
                            avatarPreview.classList.add('d-none');
                        }
                        if (avatarPlaceholder) {
                            avatarPlaceholder.classList.remove('d-none');
                        }
                        btnRemover.classList.add('d-none');

                        // Limpiar en el Navbar al instante (muestra la inicial y oculta la foto)
                        globalAvatars.forEach(img => {
                            img.classList.add('d-none');
                        });
                        if (navbarPreview) {
                            navbarPreview.classList.add('d-none');
                        }
                        if (navbarPlaceholder) {
                            navbarPlaceholder.classList.remove('d-none');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    }
});
</script>
@endsection