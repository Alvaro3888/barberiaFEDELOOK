@extends('layouts.public')

@section('content')
@if(isset($populares) && $populares->isNotEmpty())
<section class="trending-section py-5 text-white position-relative overflow-hidden">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-uppercase tracking-widest text-teal fw-bold small d-block mb-1">
                    Tendencias Fede looK
                </span>
                <h2 class="fw-bold display-6 mb-0 text-white">Lo más pedido</h2>
            </div>
            <a href="{{ route('usuario.catalogo') }}" class="text-white-50 text-decoration-none small fw-bold hover-teal transition-all">
                Ver todo el catálogo →
            </a>
        </div>

        {{-- Carrusel horizontal llamando al partial --}}
        <div class="d-flex flex-row flex-nowrap overflow-x-auto pb-4 gap-3 modern-scrollbar">
            @foreach($populares as $servicio)
                @include('usuario.servicios.partials.tarjeta-servicio', ['servicio' => $servicio])
            @endforeach
        </div>
    </div>
</section>

<style>
.trending-section {
    background-color: #0f1214;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.text-teal {
    color: #00a99d !important;
}

.hover-teal:hover {
    color: #00a99d !important;
}

.tracking-widest {
    letter-spacing: 0.18em;
}

.bg-card-dark {
    background-color: #171b1e;
    border: 1px solid rgba(255, 255, 255, 0.07) !important;
    transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.3s ease;
}

.modern-card-hover:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
    border-color: rgba(0, 169, 157, 0.45) !important;
}

.service-img {
    transition: transform 0.4s ease;
}

.modern-card-hover:hover .service-img {
    transform: scale(1.08);
}

.btn-action-circle {
    width: 32px;
    height: 32px;
    background: rgba(0, 169, 157, 0.12);
    color: #00a99d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.modern-card-hover:hover .btn-action-circle {
    background: #00a99d;
    color: #ffffff;
    transform: translateX(3px);
}

.modern-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #00a99d #171b1e;
}

.modern-scrollbar::-webkit-scrollbar {
    height: 5px;
}

.modern-scrollbar::-webkit-scrollbar-track {
    background: #121517;
    border-radius: 10px;
}

.modern-scrollbar::-webkit-scrollbar-thumb {
    background-color: #00a99d;
    border-radius: 10px;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endif

<section id="servicios" class="services-section">
<div class="container py-5">

    <div class="text-center mb-5">
        <span class="d-inline-block small fw-semibold text-uppercase services-eyebrow">
            Nuestros servicios
        </span>
        <h2 class="mt-3 fw-bold service-main-title">
            Elegí tu estilo
        </h2>
        <p class="mt-3 text-secondary mx-auto" style="max-width: 650px;">
            Servicios pensados para que tengas una experiencia completa en Fede looK.
        </p>
    </div>

    @if(isset($tipos) && $tipos->count())
        <div class="row g-4">
            @foreach($tipos as $tipo)
                @php
                    $servicioReferencia = $tipo->servicios->first();
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="{{ route('usuario.catalogo', ['tipo' => $tipo->id]) }}" class="service-card-link text-decoration-none d-block h-100">
                        <div class="service-type-card h-100 p-4 p-lg-5 rounded-4">
                            <div class="service-card-glow"></div>
                            <div class="service-icon rounded-circle d-flex align-items-center justify-content-center mb-4">
                                @if($loop->first)
                                    <svg width="30" height="30" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6l12 12M18 6L6 18"/>
                                    </svg>
                                @elseif($loop->iteration == 2)
                                    <span class="service-symbol">✂</span>
                                @else
                                    <span class="service-symbol">★</span>
                                @endif
                            </div>
                            <h3 class="fw-bold service-card-title mb-0">
                                {{ $tipo->nombre }}
                            </h3>
                            <p class="mt-3 text-secondary mb-0">
                                Servicio profesional adaptado a tu estilo.
                            </p>
                            @if($servicioReferencia)
                                <div class="service-price mt-4 fw-bold">
                                    Desde ${{ number_format($servicioReferencia->precio_actual ?? $servicioReferencia->precio, 0, ',', '.') }}
                                </div>
                            @endif
                            <div class="service-card-footer mt-4 pt-3 fw-semibold">
                                <span>Ver servicios</span>
                                <span class="service-arrow">→</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state text-center py-5 rounded-4">
            <div class="empty-state-icon mx-auto mb-3">✂</div>
            <h3 class="fw-bold">No hay servicios disponibles</h3>
            <p class="mb-0">Próximamente vas a poder consultar nuestros servicios.</p>
        </div>
    @endif

</div>
</section>

<!-- SECCIÓN CTA CORREGIDA PARA DISPOSITIVOS MÓVILES -->
<section class="booking-cta">
<div class="container">
    <div class="booking-cta-inner">
        <div class="booking-cta-content">
            <span class="section-eyebrow">Tu próximo look</span>
            <h2 class="text-white">¿Listo para tu próximo corte?</h2>
            <p class="text-white-50 mb-0">Elegí tu servicio, buscá un horario disponible y reservá tu turno de manera rápida y sencilla.</p>
        </div>
        <div class="booking-cta-action">
            <a href="{{ route('usuario.catalogo') }}" class="btn-barber">
                <span>Reservar turno</span><span>→</span>
            </a>
        </div>
    </div>
</div>
</section>

<!-- SECCIÓN ¿CÓMO FUNCIONA? -->
<section id="como-funciona" class="how-section">
<div class="how-section-overlay"></div>
<div class="container py-5 position-relative">
    <div class="section-heading text-center">
        <span class="section-eyebrow">Simple y rápido</span>
        <h2 class="section-title">¿Cómo funciona?</h2>
        <p class="section-description mx-auto">Reservar tu próximo turno nunca fue tan fácil.</p>
    </div>
    <div class="row g-4 justify-content-center">
        <div class="col-12 col-md-4">
            <div class="step-card">
                <div class="step-number">01</div>
                <div class="step-line"></div>
                <span class="step-label">Elegí</span>
                <h3>Elegí tu servicio</h3>
                <p>Seleccioná el corte, barba o servicio que quieras realizarte.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="step-card">
                <div class="step-number">02</div>
                <div class="step-line"></div>
                <span class="step-label">Reservá</span>
                <h3>Elegí día y horario</h3>
                <p>Encontrá un horario disponible que se adapte a vos.</p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="step-card">
                <div class="step-number">03</div>
                <div class="step-line"></div>
                <span class="step-label">Disfrutá</span>
                <h3>Viví la experiencia</h3>
                <p>Vení a la barbería y disfrutá de tu experiencia Fede looK.</p>
            </div>
        </div>
    </div>
</div>
</section>

<!-- BANNER PARALLAX DE LUCES -->
<section class="position-relative overflow-hidden text-white py-5 my-0" style="min-height: 400px; display: flex; align-items: center;">
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('images/luces-barberia.jpeg') }}'); background-size: cover; background-position: center; z-index: 0; background-attachment: fixed;"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: rgba(15, 18, 20, 0.82); z-index: 1;"></div>
    <div class="container position-relative text-center py-5" style="z-index: 2;">
        <div class="mx-auto" style="max-width: 700px;">
            <h3 class="fw-bold mb-3 text-white fs-2" style="text-shadow: 0 2px 12px rgba(0,0,0,0.6);">Precisión y estilo en cada detalle</h3>
            <p class="text-light opacity-85 small mb-4" style="line-height: 1.8; font-weight: 300; font-size: 0.95rem;">
                Visitá nuestras sucursales y viví una experiencia de cuidado personal diseñada exclusivamente para el hombre moderno. Encontrá tu espacio Fede looK más cercano.
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('sucursales.public') }}" class="btn btn-outline-light px-4 py-3 rounded-pill fw-bold text-uppercase small shadow-sm" style="letter-spacing: 0.12em; border-color: rgba(255,255,255,0.4); transition: all 0.3s ease;">Conocé nuestras sucursales</a>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN NUESTRO STAFF (CORREGIDA PARA QUE NO SE CORTEN EN MÓVIL) -->
<section class="py-5 bg-light">
    <div class="container py-4">
        
        <div class="text-center mb-5">
            <span class="badge px-3 py-2 rounded-pill fw-semibold mb-2 shadow-sm" style="background: #E8F5F3; color: #168F87;">Nuestro Staff</span>
            <h2 class="fw-bold text-dark mb-2">Conocé a nuestros Profesionales</h2>
            <p class="text-muted small">El equipo de expertos disponible en nuestras sucursales.</p>
        </div>

        @if(isset($barberos) && $barberos->count() > 0)
            <div class="staff-scroll-container d-flex flex-row flex-nowrap overflow-x-auto pb-4 gap-3 modern-scrollbar justify-content-lg-center">
                @foreach($barberos as $barbero)
                    <div class="staff-card-wrapper" style="min-width: 170px; max-width: 180px; flex: 0 0 170px;">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3 h-100 bg-white">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center p-1">
                                
                                <div class="mb-2">
                                    @php
                                        $avatar = $barbero->avatar ?? null;
                                        $isGoogleAvatar = $avatar && (\Illuminate\Support\Str::startsWith($avatar, ['http://', 'https://']));
                                    @endphp

                                    @if($avatar)
                                        <img src="{{ $isGoogleAvatar ? $avatar : asset('storage/' . $avatar) }}" 
                                               alt="{{ $barbero->nombre ?? $barbero->name }}" 
                                               class="rounded-circle shadow-sm object-fit-cover border border-3 border-white" 
                                               style="width: 105px; height: 105px;">
                                    @else
                                        <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center shadow-sm border border-3 border-white mx-auto" style="width: 105px; height: 105px; font-size: 36px; font-weight: bold;">
                                            {{ strtoupper(substr($barbero->nombre ?? $barbero->name ?? 'B', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>

                                <div class="w-100">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 0.9rem;">{{ $barbero->nombre ?? $barbero->name }}</h6>
                                    @if($barbero->sucursal)
                                        <span class="badge bg-light text-muted border px-2 py-1 rounded-pill fw-normal d-inline-block text-truncate w-100" style="font-size: 10.5px;">
                                            <i class="bi bi-geo-alt-fill me-1" style="color: #168F87;"></i>{{ $barbero->sucursal->nombre }}
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-3">
                <p class="small mb-0">No hay profesionales para mostrar en este momento.</p>
            </div>
        @endif

    </div>
</section>

<!-- SECCIÓN DE EXPERIENCIAS DE CLIENTES -->
@if(isset($testimonios) && $testimonios->isNotEmpty())
<section class="testimonials-section py-5">
    <div class="container py-4">
        <div class="section-heading text-center mb-5">
            <span class="section-eyebrow text-teal fw-semibold small text-uppercase">Opiniones reales</span>
            <h2 class="section-title text-dark fw-bold mt-2">Experiencias de nuestros clientes</h2>
            <p class="section-description mx-auto text-secondary">
                Descubrí lo que dicen quienes confían en nuestro trabajo día a día.
            </p>
        </div>

        <div class="row g-4 justify-content-start">
            @foreach($testimonios as $testimonio)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="testimonial-card h-100 p-4 rounded-4 shadow-sm border border-secondary-subtle" style="background-color: #ffffff !important; color: #212529 !important;">
                        <div class="d-flex align-items-center mb-3">
                            @php
                                $avatarCliente = $testimonio->cliente->avatar ?? null;
                                $isGoogleAvatarCliente = $avatarCliente && (\Illuminate\Support\Str::startsWith($avatarCliente, ['http://', 'https://']));
                            @endphp

                            @if($avatarCliente)
                                <img src="{{ $isGoogleAvatarCliente ? $avatarCliente : asset('storage/' . $avatarCliente) }}" 
                                     alt="{{ $testimonio->cliente->nombre ?? $testimonio->cliente->name ?? 'Cliente' }}" 
                                     class="rounded-circle shadow-sm object-fit-cover me-3 border border-2 border-white" 
                                     style="width: 48px; height: 48px;">
                            @else
                                <div class="testimonial-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3 shadow-sm" style="width: 48px; height: 48px; background: #06b6d4;">
                                    {{ strtoupper(substr($testimonio->cliente->nombre ?? $testimonio->cliente->name ?? 'C', 0, 1)) }}
                                </div>
                            @endif

                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-bold text-dark text-truncate">
                                    {{ $testimonio->cliente->nombre ?? $testimonio->cliente->name ?? 'Cliente Fede looK' }}
                                </h6>
                                <small class="text-muted d-block text-truncate">
                                    {{ $testimonio->turno->servicio->nombre ?? 'Servicio de Barbería' }}
                                </small>
                            </div>
                        </div>

                        <div class="text-warning mb-3 fs-6">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $testimonio->puntuacion ? '-fill' : '' }}"></i>
                            @endfor
                        </div>

                        @if($testimonio->comentario)
                            <p class="mb-0 fst-italic small text-secondary" style="line-height: 1.6;">
                                "{{ $testimonio->comentario }}"
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('sucursales.public') }}#testimonios" class="btn btn-dark fw-bold px-4 py-2 rounded-pill shadow-sm border border-secondary">
                Ver todas las opiniones →
            </a>
        </div>
    </div>
</section>
@endif

<style>
/* Estilos generales y correcciones responsivas */
.text-teal { color: #00a99d !important; }
.hover-teal:hover { color: #00a99d !important; }
.tracking-widest { letter-spacing: 0.18em; }

.service-type-card:hover .service-icon {
    transform: scale(1.1) rotate(-5deg);
    background: #00a99d !important;
    color: #ffffff !important;
}

.service-type-card:hover .service-arrow {
    transform: translateX(5px);
    background: #00a99d !important;
    color: #ffffff !important;
}

.step-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
}
.step-card:hover {
    transform: translateY(-7px) !important;
    box-shadow: 0 18px 36px rgba(0,0,0,.15) !important;
}

.services-section, .booking-cta, .how-section {
    --title-dark: #151515;
    --gray: #777777;
    --gradient-main: linear-gradient(120deg, #00a878 0%, #00a99d 52%, #16849f 100%);
}
.services-section { background: linear-gradient(180deg, #ffffff 0%, #f7f7f7 100%); }
.service-card-link { color: inherit; }
.service-type-card {
    position: relative; isolation: isolate; overflow: hidden; background: #fafafa;
    border: 1px solid rgba(0, 0, 0, .11); box-shadow: 0 8px 24px rgba(0, 0, 0, .055);
    transition: transform .28s ease, box-shadow .28s ease;
}
.service-type-card:hover {
    transform: translateY(-8px); background: #ffffff; border-color: rgba(0, 169, 157, .35);
    box-shadow: 0 20px 42px rgba(0, 0, 0, .10);
}
.service-icon {
    width: 64px; height: 64px; margin-top: .4rem; background: #eeeeee; color: #171717; border: 1px solid rgba(0, 0, 0, .18);
}
.services-eyebrow { color: #00847d !important; letter-spacing: .13em; font-weight: 900 !important; }
.service-main-title { color: var(--title-dark) !important; font-size: 2.5rem; font-weight: 900 !important; }
.service-price { color: #00847d; font-size: 1.05rem; }
.service-card-footer { display: flex; align-items: center; justify-content: space-between; color: var(--title-dark); border-top: 1px solid rgba(0, 0, 0, .11); }
.service-arrow {
    display: inline-flex; align-items: center; justify-content: center; width: 33px; height: 33px;
    color: #00847d; background: #ffffff; border: 1.5px solid rgba(0, 169, 157, .55); border-radius: 50%;
}

.booking-cta { position: relative; padding: 4rem 0; background: #0d0d0d; overflow: hidden; }
.booking-cta-inner {
    position: relative; 
    display: flex; 
    flex-wrap: wrap; 
    align-items: center; 
    justify-content: space-between; 
    gap: 2rem;
    padding: 2.5rem 2rem; 
    background: linear-gradient(135deg, #1b1b1b 0%, #151515 55%, #101010 100%);
    border: 1px solid rgba(255,255,255,.13); 
    border-radius: 20px; 
    box-shadow: 0 18px 40px rgba(0,0,0,.28);
}
.booking-cta-content { flex: 1 1 300px; }
.booking-cta-action { flex-shrink: 0; }

@media (min-width: 768px) {
    .booking-cta-inner { padding: 3rem 3.5rem; }
}

.btn-barber {
    display: inline-flex; align-items: center; justify-content: center; gap: .9rem; min-width: 190px;
    padding: .95rem 1.4rem; color: #ffffff; background: var(--gradient-main); border: 0; border-radius: 7px;
    text-decoration: none; font-size: .86rem; font-weight: 900; box-shadow: 0 8px 20px rgba(0,0,0,.38);
}

.how-section { position: relative; overflow: hidden; background-image: url("{{ asset('images/hero-madera.jpg') }}"); background-size: cover; background-position: center center; }
.how-section-overlay { position: absolute; inset: 0; background: rgba(255, 255, 255, .40); z-index: 1; pointer-events: none; }
.section-heading { position: relative; z-index: 3; margin-bottom: 4rem; }
.section-eyebrow { display: inline-flex; align-items: center; color: #00847d; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .2em; }
.section-title { margin-top: 1rem; margin-bottom: 1rem; color: var(--title-dark); font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; }
.section-description { max-width: 680px; margin-top: 1.25rem; color: #3f3f3f; font-size: 1rem; line-height: 1.8; }
.step-card {
    position: relative; z-index: 3; height: 100%; padding: 2rem; text-align: center;
    background: rgba(255,255,255,.56); border: 1px solid rgba(255,255,255,.72); border-radius: 18px; box-shadow: 0 12px 30px rgba(0,0,0,.12);
}
.step-number {
    width: 78px; height: 78px; display: flex; align-items: center; justify-content: center; margin: 0 auto;
    color: #ffffff; background: var(--gradient-main); border-radius: 50%; font-size: 1rem; font-weight: 900;
}
.step-line { width: 65px; height: 3px; margin: 1.6rem auto; background: var(--gradient-main); border-radius: 50px; }
.step-label { display: block; margin-bottom: .5rem; color: #00786f; font-size: .68rem; font-weight: 900; text-transform: uppercase; letter-spacing: .18em; }

.testimonial-card {
    background-color: #171b1e !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    transition: transform 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.3s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.3s ease;
}
.testimonial-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
    border-color: rgba(0, 169, 157, 0.45) !important;
}

/* Scroll horizontal suave para el Staff en móviles */
.staff-scroll-container {
    scrollbar-width: thin;
    scrollbar-color: #168F87 #f1f1f1;
    -webkit-overflow-scrolling: touch;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}
.staff-scroll-container::-webkit-scrollbar { height: 4px; }
.staff-scroll-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.staff-scroll-container::-webkit-scrollbar-thumb { background-color: #168F87; border-radius: 10px; }

@media (min-width: 992px) {
    .staff-scroll-container {
        overflow-x: visible !important;
        flex-wrap: wrap !important;
        justify-content: center !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() { 
    var modalElement = document.getElementById('modalCalificacionFlotante'); 
    if (modalElement) { 
        var modalCalificacion = new bootstrap.Modal(modalElement); 
        modalCalificacion.show(); 
    } 
    const starLabels = document.querySelectorAll('.star-label-flotante'); 
    const comentarioInput = document.getElementById('comentario_flotante'); 
    const btnEnviar = document.getElementById('btnEnviarCalificacion'); 
    const errorMsg = document.getElementById('error-comentario-flotante'); 
    
    function validarFormulario() { 
        const estrellaSeleccionada = document.querySelector('input[name="puntuacion"]:checked'); 
        const texto = comentarioInput ? comentarioInput.value.trim() : ''; 
        const empiezaConLetra = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ]/.test(texto); 
        const cumpleMinimo = texto.length >= 3; 
        
        if (estrellaSeleccionada && empiezaConLetra && cumpleMinimo) { 
            btnEnviar.removeAttribute('disabled'); 
            if (errorMsg) errorMsg.classList.add('d-none'); 
        } else { 
            btnEnviar.setAttribute('disabled', 'true'); 
            if (texto.length > 0 && (!empiezaConLetra || !cumpleMinimo)) { 
                if (errorMsg) errorMsg.classList.remove('d-none'); 
            } else { 
                if (errorMsg) errorMsg.classList.add('d-none'); 
            } 
        } 
    } 
    
    starLabels.forEach((label) => { 
        label.addEventListener('click', function() { 
            const selectedValue = parseInt(this.getAttribute('data-value')); 
            const radioInput = document.getElementById('star_flotante_' + selectedValue); 
            if (radioInput) { 
                radioInput.checked = true; 
                validarFormulario(); 
            } 
            starLabels.forEach((l) => { 
                const val = parseInt(l.getAttribute('data-value')); 
                if (val <= selectedValue) { 
                    l.classList.add('activa'); 
                } else { 
                    l.classList.remove('activa'); 
                } 
            }); 
        }); 
    }); 
    
    if (comentarioInput) { 
        comentarioInput.addEventListener('input', validarFormulario); 
    } 
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollContainer = document.querySelector('.modern-scrollbar');
    if (!scrollContainer) return;

    let scrollSpeed = 1;
    let autoScroll = setInterval(() => {
        scrollContainer.scrollLeft += scrollSpeed;
        
        if (scrollContainer.scrollLeft >= (scrollContainer.scrollWidth - scrollContainer.clientWidth)) {
            scrollContainer.scrollLeft = 0;
        }
    }, 30);

    scrollContainer.addEventListener('mouseenter', () => clearInterval(autoScroll));
    scrollContainer.addEventListener('mouseleave', () => {
        autoScroll = setInterval(() => {
            scrollContainer.scrollLeft += scrollSpeed;
            if (scrollContainer.scrollLeft >= (scrollContainer.scrollWidth - scrollContainer.clientWidth)) {
                scrollContainer.scrollLeft = 0;
            }
        }, 30);
    });
});
</script>
@endsection