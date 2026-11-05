@extends('layouts.public')

@section('content')

<!-- HERO / ENCABEZADO DE SUCURSALES -->
<section class="py-5 position-relative overflow-hidden text-white" style="background: linear-gradient(135deg, #111111 0%, #1f2937 100%);">
    <div class="container py-4 position-relative z-2 text-center">
        <span class="badge px-3 py-2 rounded-pill text-uppercase mb-3 shadow-sm fw-bold" style="background: #00a99d; color: #fff; letter-spacing: 2px; font-size: 0.75rem;">
            Red Oficial Fede looK
        </span>
        <h1 class="display-4 fw-extrabold text-white mb-2" style="font-family: 'Bebas Neue', sans-serif; letter-spacing: 1.5px;">
            Nuestras Sucursales
        </h1>
        <p class="text-white-50 mx-auto mb-0" style="max-width: 600px; font-size: 1.05rem;">
            Elegí la sede que te quede más cómoda y consultá su ubicación e información de contacto.
        </p>
    </div>
</section>

<!-- SECCIÓN DE SUCURSALES -->
<section id="sucursales" class="py-5 bg-white position-relative">
    <div class="container py-4">

        <div class="row g-4 justify-content-center">
            @forelse($sucursales as $sucursal)
                <div class="col-12">
                    
                    <div class="p-4 p-lg-4 rounded-4 border border-light-subtle shadow-sm bg-white transition-all">
                        <div class="row align-items-center g-4">
                            
                            <!-- Columna 1: Imagen -->
                            <div class="col-lg-4">
                                <div class="rounded-4 overflow-hidden shadow-sm bg-light position-relative" style="height: 220px;">
                                    @if(!empty($sucursal->imagen))
                                        <img src="{{ asset('storage/' . $sucursal->imagen) }}" alt="{{ $sucursal->nombre }}" class="w-100 h-100 object-fit-cover">
                                    @elseif(!empty($sucursal->imagen_url))
                                        <img src="{{ $sucursal->imagen_url }}" alt="{{ $sucursal->nombre }}" class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted bg-light border">
                                            <span class="fs-1">💈</span>
                                            <span class="small fw-semibold mt-2 text-secondary">Fede looK Sede</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Columna 2: Información de la Sede -->
                            <div class="col-lg-4 d-flex flex-column justify-content-center">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge rounded-pill px-3 py-1 fw-bold text-uppercase" style="background: rgba(0, 169, 157, 0.12); color: #00a99d; font-size: 0.75rem;">
                                        Sede Habilitada
                                    </span>
                                </div>
                                
                                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.7rem; letter-spacing: -0.01em;">
                                    {{ $sucursal->nombre }}
                                </h3>

                                <div class="d-flex flex-column gap-2 text-secondary small">
                                    <div class="d-flex align-items-start gap-2">
                                        <span class="icon-circle text-teal flex-shrink-0">📍</span> 
                                        <span><strong>Dirección:</strong> {{ $sucursal->direccion }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="icon-circle text-teal flex-shrink-0">📞</span> 
                                        <span><strong>Teléfono:</strong> {{ $sucursal->telefono }}</span>
                                    </div>
                                    @if(!empty($sucursal->horarios))
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="icon-circle text-teal flex-shrink-0">⏰</span> 
                                            <span><strong>Horarios:</strong> {{ $sucursal->horarios }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Columna 3: Mapa Interactivo -->
                            <div class="col-lg-4">
                                <div class="rounded-4 overflow-hidden border border-light shadow-sm bg-light" style="height: 200px;">
                                    @php
                                        $mapa = trim($sucursal->mapa_url ?? '');
                                    @endphp

                                    @if(!empty($mapa))
                                        @if(str_contains($mapa, '<iframe'))
                                            {!! $mapa !!}
                                        @else
                                            <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3 text-center bg-white">
                                                <span class="fs-2 mb-2">🗺️</span>
                                                <a href="{{ $mapa }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-dark fw-bold px-3 rounded-pill shadow-sm">
                                                    Ver en Google Maps
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                            <span class="fs-2 mb-1">📍</span>
                                            <span class="small">Mapa no disponible</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No hay sucursales disponibles por el momento.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- SECCIÓN DE CONTACTO DIRECTO -->
<section class="py-5" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            
            <div class="col-lg-5">
                <span class="text-uppercase text-teal fw-bold small" style="letter-spacing: 2px;">Atención al Cliente</span>
                <h2 class="fw-bold display-6 text-dark mt-2 mb-3">¿Tenés alguna duda o consulta?</h2>
                <p class="text-secondary mb-4">
                    Escribinos de forma directa mediante este formulario y nos pondremos en contacto con vos a la brevedad a tu correo electrónico.
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white p-3 rounded-circle text-teal border shadow-sm">
                            <i class="bi bi-headset fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Soporte ágil</h6>
                            <small class="text-muted">Atención personalizada de lunes a sábados.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white p-3 rounded-circle text-teal border shadow-sm">
                            <i class="bi bi-envelope-check fs-5"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Respuesta directa</h6>
                            <small class="text-muted">Comunicación directa con la administración.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white border">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('sucursales.contacto') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Nombre y Apellido</label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control rounded-3 border bg-light py-2.5 px-3 shadow-none" 
                                       placeholder="Tu nombre" 
                                       value="{{ old('nombre') }}" 
                                       oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')"
                                       maxlength="50"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Correo Electrónico</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control rounded-3 border bg-light py-2.5 px-3 shadow-none" 
                                       placeholder="ejemplo@correo.com" 
                                       value="{{ old('email') }}" 
                                       oninput="this.value = this.value.toLowerCase().replace(/\s/g, '')"
                                       maxlength="100"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Teléfono</label>
                                <input type="tel" 
                                       name="telefono" 
                                       class="form-control rounded-3 border bg-light py-2.5 px-3 shadow-none" 
                                       placeholder="Ej: 3881234567" 
                                       value="{{ old('telefono') }}"
                                       oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                                       maxlength="18">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">Sucursal de Referencia</label>
                                <select name="sucursal_id" class="form-select rounded-3 border bg-light py-2.5 px-3 shadow-none">
                                    <option value="">Consulta general</option>
                                    @foreach($sucursales as $suc)
                                        <option value="{{ $suc->id }}" {{ old('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold small text-dark">Mensaje</label>
                                <textarea name="mensaje" 
                                          class="form-control rounded-3 border bg-light py-3 px-3 shadow-none" 
                                          rows="4" 
                                          placeholder="¿En qué podemos ayudarte?" 
                                          maxlength="1000"
                                          required>{{ old('mensaje') }}</textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-teal fw-bold px-4 py-3 rounded-pill w-100 shadow-sm">
                                    Enviar Mensaje
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN DE EXPERIENCIAS DE CLIENTES -->
@php
    $testimonios = $testimonios ?? \App\Models\Calificacion::with(['cliente', 'turno.servicio', 'turno.barbero'])
        ->whereIn('puntuacion', [4, 5])
        ->where('activo', true)
        ->latest('created_at')
        ->get();
@endphp

@if($testimonios->isNotEmpty())
<section id="testimonios" class="testimonials-section py-5 bg-white border-top">
    <div class="container py-4" style="max-width: 900px;">
        <div class="section-heading text-center mb-5">
            <span class="section-eyebrow">Opiniones reales</span>
            <h2 class="section-title">Experiencias de nuestros clientes</h2>
            <p class="section-description mx-auto text-muted small">
                Descubrí lo que dicen quienes confían en nuestro trabajo día a día.
            </p>
        </div>

        <div class="testimonials-list-wrapper bg-light border border-secondary-subtle rounded-4 shadow-sm overflow-hidden">
            <div class="testimonials-scroll-list p-3 p-md-4">
                @foreach($testimonios as $testimonio)
                    <div class="testimonial-row py-3 px-2 border-bottom border-light-subtle">
                        <div class="row align-items-start g-3">
                            
                            <div class="col-12 col-md-4">
                                <div class="d-flex align-items-center">
                                    <div class="testimonial-avatar rounded-circle d-flex align-items-center justify-content-center fw-bold text-white me-3 flex-shrink-0 shadow-sm" style="width: 44px; height: 44px; background: #00a99d;">
                                        {{ strtoupper(substr($testimonio->cliente->nombre ?? $testimonio->cliente->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mb-0 fw-bold text-dark text-truncate">
                                            {{ $testimonio->cliente->nombre ?? $testimonio->cliente->name ?? 'Cliente Fede looK' }}
                                        </h6>
                                        <span class="text-muted small d-block text-truncate" style="font-size: 0.8rem;">
                                            {{ $testimonio->turno->servicio->nombre ?? 'Servicio de Barbería' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="d-flex flex-column gap-1">
                                    <div class="text-warning fs-6">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $testimonio->puntuacion ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-dark mb-0 fst-italic" style="line-height: 1.6; font-size: 0.95rem; word-break: break-word;">
                                        "{{ $testimonio->comentario ?: 'Excelente servicio y atención muy profesional. Totalmente recomendado.' }}"
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>
@endif

<!-- ESTILOS Y COMPONENTES -->
<style>
.text-teal { color: #00a99d !important; }

.btn-teal {
    background: #00a99d;
    color: #ffffff !important;
    border: none;
    transition: all 0.3s ease;
}

.btn-teal:hover {
    background: #00847d;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 169, 157, 0.3) !important;
}

.icon-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: rgba(0, 169, 157, 0.1);
    border-radius: 50%;
    font-size: 0.85rem;
}

.transition-all {
    transition: all 0.3s ease;
}

.transition-all:hover {
    box-shadow: 0 1rem 2rem rgba(0,0,0,0.05) !important;
    border-color: rgba(0, 169, 157, 0.3) !important;
}

/* Scroll Virtual de Calificaciones intacto */
.testimonials-scroll-list {
    max-height: 520px;
    overflow-y: auto;
}

.testimonials-scroll-list::-webkit-scrollbar {
    width: 6px;
}
.testimonials-scroll-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
.testimonials-scroll-list::-webkit-scrollbar-thumb {
    background: #00a99d;
    border-radius: 4px;
}

.testimonial-row {
    transition: background-color 0.2s ease;
}

.testimonial-row:last-child {
    border-bottom: none !important;
}

.testimonial-row:hover {
    background-color: #fcfcfc;
}

.section-heading { position: relative; z-index: 3; margin-bottom: 3.5rem; }
.section-eyebrow { display: inline-flex; align-items: center; color: #00847d; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .2em; }
.section-title { margin-top: 1rem; margin-bottom: 1rem; color: #151515; font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; }
.section-description { max-width: 680px; margin-top: 1.25rem; color: #3f3f3f; font-size: 1rem; line-height: 1.8; }
</style>

@endsection