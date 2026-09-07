@php
    $layout = 'layouts.public';
    if (Auth::check()) {
        if (Auth::user()->rol === 'admin') {
            $layout = 'layouts.app'; // Admin con Panel
        } elseif (Auth::user()->rol === 'barbero') {
            $layout = 'layouts.barbero'; // Barbero con Agenda
        }
    }
@endphp

@extends($layout)
@section('content')
<div class="container">
<div class="container mt-4">
    <h2 class="mb-4 text-center text-danger">Todos los Arreglos de Barba</h2>
    <div class="row">
        @forelse($servicios as $servicio)
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                @if($servicio->imagen)
                    <img src="{{ asset('storage/'.$servicio->imagen) }}" class="card-img-top" alt="{{ $servicio->nombre }}" style="height:220px; object-fit:cover;">
                @else
                    <img src="{{ asset('images/barba1.webp') }}" class="card-img-top" alt="{{ $servicio->nombre }}" style="height:220px; object-fit:cover;">
                @endif
                <div class="card-body text-center">
                    <h6 class="card-title">{{ $servicio->nombre }}</h6>
                    <p class="card-text">Duración: {{ $servicio->duracion }} min</p>
                    <p class="card-text text-danger fw-bold">${{ $servicio->precio_actual }}</p>
                    @if(Auth::check() && Auth::user()->rol_id == 3)
                        <a href="{{ route('turnos.create') }}" class="btn btn-sm btn-danger">Reservar</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <p class="text-center">No hay arreglos de barba disponibles.</p>
        @endforelse
    </div>
</div>
@endsection
