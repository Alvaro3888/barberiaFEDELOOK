@extends('layouts.public')

@section('content')

<style> 
html { scroll-behavior: smooth; }
.service-page {
    background-color: #f3f4f6;
    background-image:
        radial-gradient(
            rgba(0, 0, 0, 0.08) 1px,
            transparent 1px
        );
    background-size: 30px 30px;
}

.service-header {
    background: rgba(255, 255, 255, 0.92);
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
}

.service-card {
    transition:
        transform 0.25s ease,
        box-shadow 0.25s ease,
        border-color 0.25s ease;
    border: 1px solid #dee2e6;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 35px rgba(0, 0, 0, 0.10) !important;
    border-color: #06b6d4 !important; /* Borde fino turquesa elegante al pasar el cursor */
}

.service-image {
    transition: transform 0.35s ease;
}

.service-card:hover .service-image {
    transform: scale(1.05);
}

.service-image-container {
    height: 240px;
    overflow: hidden;
    background: #e9ecef;
}

.service-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.back-link {
    color: #6c757d;
    transition: color 0.2s ease;
}

.back-link:hover {
    color: #000;
}

.service-badge {
    background: rgba(255, 255, 255, 0.95);
    color: #000;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: #f3f4f6;
}

@media (max-width: 767.98px) {
    .service-header {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .service-image-container {
        height: 220px;
    }
}
</style> 

<div class="service-page min-vh-100">

<section class="service-header">
    <div class="container py-5">
        <div class="mb-4">
            <a href="{{ url('/') }}" class="back-link text-decoration-none d-inline-flex align-items-center gap-2 fw-medium">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Volver al inicio
            </a>
        </div>

        <div class="col-12 col-lg-8">
            <span class="d-inline-block px-3 py-2 bg-light text-dark rounded-pill small fw-semibold text-uppercase">
                Nuestros servicios
            </span>
            <h1 class="mt-4 display-4 fw-bold text-dark">
                {{ $tipo->nombre }}
            </h1>
            <p class="mt-3 fs-5 text-secondary lh-lg mb-0">
                Conocé los servicios disponibles dentro de {{ $tipo->nombre }} y elegí el que mejor se adapte a tu estilo.
            </p>
        </div>
    </div>
</section>

<section class="py-5 py-lg-6">
    <div class="container">
        {{-- Filtramos la colección para tomar solo los servicios activos --}}
        @php
            $serviciosActivos = $tipo->servicios->where('activo', true);
        @endphp

        @if($serviciosActivos->count() > 0)
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-5">
                <div>
                    <span class="text-dark fw-semibold small text-uppercase">
                        Servicios disponibles
                    </span>
                    <h2 class="mt-2 display-6 fw-bold text-dark mb-0">
                        Elegí tu servicio
                    </h2>
                </div>
                <div class="text-secondary small">
                    {{ $serviciosActivos->count() }} {{ $serviciosActivos->count() === 1 ? 'servicio disponible' : 'servicios disponibles' }}
                </div>
            </div>

            <div class="row g-4">
                {{-- Recorremos únicamente los servicios activos --}}
                @foreach($serviciosActivos as $servicio)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route('usuario.turnos.create', ['servicio_id' => $servicio->id]) }}" class="service-card bg-white rounded-4 overflow-hidden shadow-sm h-100 d-flex flex-column text-decoration-none">
                            <div class="service-image-container position-relative">
                                @if($servicio->imagen)
                                    <img src="{{ asset('storage/' . $servicio->imagen) }}" alt="{{ $servicio->nombre }}" class="service-image">
                                @elseif($servicio->imagen_url)
                                    <img src="{{ $servicio->imagen_url }}" alt="{{ $servicio->nombre }}" class="service-image">
                                @else
                                    <img src="{{ asset('images/default-service.jpg') }}" alt="Imagen por defecto" class="service-image">
                                @endif

                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="service-badge px-3 py-2 rounded-pill small fw-semibold shadow-sm">
                                        {{ $tipo->nombre }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <h3 class="fs-4 fw-bold text-dark mb-0">
                                    {{ $servicio->nombre }}
                                </h3>

                                <p class="mt-3 text-secondary lh-lg flex-grow-1">
                                    {{ $servicio->descripcion ?? 'Servicio profesional de Fede looK pensado para complementar tu estilo.' }}
                                </p>

                                <div class="mt-4 pt-4 border-top">
                                    <div class="d-flex align-items-center justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-2 text-secondary">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="small">{{ $servicio->duracion }} min</span>
                                        </div>

                                        @auth
                                            <span class="fs-5 fw-bold text-dark">
                                                ${{ number_format($servicio->precio_actual ?? $servicio->precio ?? 0, 2) }}
                                            </span>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-4 border shadow-sm p-5 text-center">
                <div class="empty-icon mx-auto rounded-circle d-flex align-items-center justify-content-center text-secondary mb-3">
                    <svg width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.5m0 3h.01M10.29 3.86l-7.82 13.5A2 2 0 004.2 20h15.6a2 2 0 001.73-2.64l-7.82-13.5a2 2 0 00-3.42 0z"></path>
                    </svg>
                </div>
                <h3 class="mt-4 fs-4 fw-bold text-dark">No hay servicios disponibles</h3>
                <p class="mt-2 text-secondary">Actualmente no hay servicios activos dentro de este tipo.</p>
                <a href="{{ url('/') }}" class="btn btn-dark d-inline-flex align-items-center gap-2 mt-3 px-4 py-3 rounded-3 text-decoration-none fw-semibold">
                    Volver al inicio
                </a>
            </div>
        @endif
    </div>
</section>

</div>
@endsection