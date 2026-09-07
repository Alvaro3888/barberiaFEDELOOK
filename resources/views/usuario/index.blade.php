@extends('layouts.public')

@section('content')
<div class="index-wrapper">

    <!-- Hero principal con imagen de fondo -->
    <section class="hero text-center" 
             style="background-image: url('{{ $heroImagen }}'); 
                    background-size: cover; 
                    background-position: center; 
                    min-height: 80vh; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center;">
        <div class="overlay" 
             style="background-color: rgba(0,0,0,0.7); 
                    padding: 60px; 
                    border-radius: 12px; 
                    text-align: center; 
                    max-width: 800px; 
                    width: 90%;">
            <h1 class="hero-title" style="color: #fff;">TU CORTE, TU PALABRA.</h1>
            <p class="hero-text mt-3" style="color: #f1f1f1;">
                Reservá turno con tu barbero de confianza. La seña asegura tu lugar en la agenda y se devuelve si cancelás con anticipación.
            </p>
            <div class="hero-actions mt-4">
                <button type="button" 
                        onclick="window.location.href='{{ route('usuario.turnos.create') }}'" 
                        class="btn-oxblood">
                    Solicitar turno
                </button>
            </div>
        </div>
    </section>

    <!-- Servicios destacados -->
    <section class="mt-5 section">
        <h2 class="section-title">SERVICIOS</h2>
        <div class="row justify-content-center">
            @foreach($servicios->take(4) as $servicio)
                <div class="col-md-3 mb-4">
                    <div class="card service-card border-ink h-100 shadow-sm">
                        <img src="{{ $servicio->imagenes->first()->path ?? asset('images/default_servicio.jpg') }}" 
                             alt="{{ $servicio->nombre }}" 
                             class="card-img-top rounded-top">
                        <div class="card-body text-center bg-paper-light">
                            <h5 class="card-title">{{ $servicio->nombre }}</h5>
                            <p class="hero-text text-charcoal">{{ $servicio->descripcion }}</p>
                            @auth
                                <p class="card-price text-brass">${{ $servicio->precio_actual }}</p>
                            @else
                                <p class="locked text-muted"><i class="bi bi-lock"></i> Ver precio</p>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Sucursales -->
    <section class="mt-5 section">
        <h2 class="section-title">SUCURSALES</h2>
        <p class="hero-text">Cada una con su agenda propia</p>
        <div class="row justify-content-center">
            @foreach($sucursales as $sucursal)
                <div class="col-md-4 mb-4">
                    <div class="card bg-paper-light border-ink p-3 h-100 shadow-sm">
                        <img src="{{ $sucursal->imagenes->first()->path ?? asset('images/default_sucursal.jpg') }}" 
                             alt="{{ $sucursal->nombre }}" 
                             class="card-img-top rounded-top mb-3">
                        <h5 class="text-ink">{{ $sucursal->nombre }}</h5>
                        <p class="text-charcoal">{{ $sucursal->descripcion }}</p>
                        <p class="text-muted">{{ $sucursal->horario }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Call to action final -->
    <section class="mt-5 section text-center">
        <h2 class="section-title">RESERVÁ AHORA</h2>
        <p class="hero-text">Tu lugar está a un clic de distancia.</p>
        <button type="button" 
                onclick="window.location.href='{{ route('usuario.turnos.create') }}'" 
                class="btn-oxblood mt-3">
            Reservar turno
        </button>
    </section>

</div>
@endsection
