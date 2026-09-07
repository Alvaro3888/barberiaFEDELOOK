@extends('layouts.guest')


@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0" style="max-width: 340px; width: 100%;">
        
        <!-- Imagen/logo más pequeña -->
        <div class="text-center bg-light">
            <img src="{{ asset('images/logofedelook.jpg') }}" alt="Logo Fedelook" 
                 style="width:100%; height:120px; object-fit:contain; border-bottom: 2px solid #dc3545;">
        </div>

        <!-- Formulario debajo de la imagen -->
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nombre -->
                <div class="mb-3">
                    <input id="nombre" type="text" 
                           class="form-control bg-light text-dark @error('nombre') is-invalid @enderror"
                           name="nombre" value="{{ old('nombre') }}" required autofocus
                           placeholder="Nombre completo">
                    @error('nombre')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div class="mb-3">
                    <input id="telefono" type="text" 
                           class="form-control bg-light text-dark @error('telefono') is-invalid @enderror"
                           name="telefono" value="{{ old('telefono') }}" required
                           placeholder="Teléfono">
                    @error('telefono')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <input id="email" type="email" 
                           class="form-control bg-light text-dark @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required
                           placeholder="Correo electrónico">
                    @error('email')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <input id="password" type="password" 
                           class="form-control bg-light text-dark @error('password') is-invalid @enderror"
                           name="password" required
                           placeholder="Contraseña">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <input id="password_confirmation" type="password" 
                           class="form-control bg-light text-dark"
                           name="password_confirmation" required
                           placeholder="Confirmar contraseña">
                </div>

                <!-- Botón de registro -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger btn-sm">Registrarse</button>
                </div>
            </form>

            <!-- Login link -->
            <div class="text-center mt-2">
                <p class="mb-1">¿Ya tenés cuenta?</p>
                <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm">
                    Iniciar sesión
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
