@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 py-md-5 px-lg-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- ENCABEZADO ESTILO SUCURSALES -->
    <div class="p-4 p-lg-5 rounded-4 text-white mb-4 position-relative overflow-hidden shadow-sm animate-fade-in" style="background: linear-gradient(135deg, #111111 0%, #1f2937 100%);">
        <div class="position-relative z-2">
            <span class="badge px-3 py-2 rounded-pill text-uppercase mb-2 shadow-sm fw-bold btn-no-select" style="background: #00a99d; color: #fff; letter-spacing: 1.5px; font-size: 0.7rem;">
                Ajustes Generales
            </span>
            <h1 class="display-6 fw-bold mb-1 text-white" style="letter-spacing: -0.01em;">
                Configuración de Contacto y Redes
            </h1>
            <p class="text-white-50 mb-0 small" style="max-width: 600px; font-size: 0.95rem;">
                Actualizá en tiempo real el número de WhatsApp oficial, los accesos a las redes sociales de la barbería visibles en la web pública y gestioná las copias de seguridad.
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-fade-in" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4 animate-fade-in" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- TARJETA DE FORMULARIO DE CONFIGURACIÓN -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 card-modern">
        <div class="card-body p-4 p-lg-5">
            <form action="{{ route('admin.configuracion.update') }}" method="POST" class="form-action-prevent">
                @csrf

                <div class="row g-4">
                    
                    <!-- WHATSAPP NÚMERO -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-whatsapp text-success me-1"></i> Número de WhatsApp
                        </label>
                        <input type="text" name="whatsapp_numero" value="{{ old('whatsapp_numero', $config['whatsapp_numero']) }}" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="Ej: 5493881234567" required>
                        <div class="form-text text-muted" style="font-size: 0.78rem;">Incluí código de país y área sin símbolos (Ej: 549...).</div>
                        @error('whatsapp_numero')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- WHATSAPP MENSAJE PREDETERMINADO -->
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-chat-text text-teal me-1"></i> Mensaje Predeterminado de WhatsApp
                        </label>
                        <input type="text" name="whatsapp_mensaje" value="{{ old('whatsapp_mensaje', $config['whatsapp_mensaje']) }}" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="Ej: Hola, quiero consultar por un turno">
                        <div class="form-text text-muted" style="font-size: 0.78rem;">Texto que aparecerá escrito al abrir el chat directo.</div>
                        @error('whatsapp_mensaje')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12"><hr class="text-muted opacity-25 my-2"></div>

                    <!-- INSTAGRAM -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-instagram text-danger me-1"></i> Enlace de Instagram
                        </label>
                        <input type="url" name="instagram_url" value="{{ old('instagram_url', $config['instagram_url']) }}" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="https://instagram.com/tu_barberia">
                        @error('instagram_url')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- FACEBOOK -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-facebook text-primary me-1"></i> Enlace de Facebook
                        </label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $config['facebook_url']) }}" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="https://facebook.com/tu_barberia">
                        @error('facebook_url')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- TIKTOK -->
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-tiktok text-dark me-1"></i> Enlace de TikTok
                        </label>
                        <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $config['tiktok_url']) }}" class="form-control rounded-3 border bg-light py-2 px-3 shadow-none" placeholder="https://tiktok.com/@tu_barberia">
                        @error('tiktok_url')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- BOTÓN DE GUARDAR CONFIGURACIÓN -->
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn text-white px-4 py-2 rounded-pill fw-bold shadow-sm transition-all btn-with-spinner btn-no-select" style="background-color: #00a99d;">
                            <span class="btn-text"><i class="bi bi-save me-2"></i> Guardar Cambios</span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- TARJETA DE RESPALDOS Y BASE DE DATOS -->
    <div class="card border-0 shadow-sm rounded-4 bg-white card-modern">
        <div class="card-body p-4 p-lg-5">
            <h4 class="fw-bold text-dark mb-3">
                <i class="bi bi-database text-teal me-2"></i> Respaldos y Base de Datos
            </h4>
            <p class="text-muted small mb-4">
                Configurá cada cuánto tiempo querés que el sistema genere una copia de seguridad automática o descargala manualmente de inmediato.
            </p>

            <!-- Formulario para frecuencia automática -->
            <form action="{{ route('admin.backup.frecuencia') }}" method="POST" class="mb-4 form-action-prevent">
                @csrf
                <label class="form-label small fw-bold text-dark mb-1">Frecuencia de respaldo automático</label>
                <div class="input-group" style="max-width: 500px;">
                    <select name="backup_frecuencia" class="form-select rounded-start-3 border bg-light py-2 px-3 shadow-none">
                        <option value="1" {{ ($frecuenciaActual ?? 7) == 1 ? 'selected' : '' }}>Cada 1 día</option>
                        <option value="3" {{ ($frecuenciaActual ?? 7) == 3 ? 'selected' : '' }}>Cada 3 días</option>
                        <option value="7" {{ ($frecuenciaActual ?? 7) == 7 ? 'selected' : '' }}>Cada 7 días (Recomendado)</option>
                        <option value="30" {{ ($frecuenciaActual ?? 7) == 30 ? 'selected' : '' }}>Cada 30 días</option>
                    </select>
                    <button type="submit" class="btn text-white px-4 rounded-end-3 fw-bold btn-with-spinner btn-no-select" style="background-color: #00a99d;">
                        <span class="btn-text">Guardar Frecuencia</span>
                    </button>
                </div>
            </form>

            <hr class="text-muted opacity-25 my-4">

            <!-- Botón para descargar de forma manual -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Copia de seguridad manual</h6>
                    <p class="text-muted small mb-0">Generá y descargá el archivo comprimido (.zip) de la base de datos al instante.</p>
                </div>
                <form action="{{ route('admin.backup.generar') }}" method="POST" class="form-action-prevent">
                    @csrf
                    <button type="submit" class="btn btn-dark text-nowrap rounded-pill px-4 py-2 fw-semibold shadow-sm btn-with-spinner btn-no-select">
                        <span class="btn-text"><i class="bi bi-download me-1"></i> Descargar Backup Ahora</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

<style>
.text-teal { color: #00a99d !important; }
.transition-all { transition: all 0.2s ease; }
.transition-all:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
.card-modern {
    box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
    border: none;
}
.btn-no-select {
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
/* Spinner de Tijeras Animadas */
.scissors-spin {
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Prevenir selección por dobles clics rápidos
    document.querySelectorAll('.btn-no-select').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            if (e.detail > 1) e.preventDefault();
        });
    });

    // SISTEMA ANTI-DOBLE CLIC Y SPINNER CON TIJERAS (Validación de 250ms)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('form-action-prevent')) {
            const submitBtn = form.querySelector('.btn-with-spinner');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                
                const textSpan = submitBtn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.dataset.original = textSpan.innerHTML;
                    setTimeout(() => {
                        textSpan.innerHTML = `<i class="bi bi-scissors scissors-spin me-1"></i> Procesando...`;
                    }, 250);
                }
            }
        }
    });
});
</script>
@endsection