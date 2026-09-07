@extends('layouts.guest')


@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card shadow-sm border-0" style="max-width: 320px; width: 100%;">
        
        <!-- Imagen/logo más pequeña -->
        <div class="text-center bg-light">
            <img src="{{ asset('images/logofedelook.jpg') }}" alt="Logo Fedelook" 
                 style="width:100%; height:120px; object-fit:contain; border-bottom: 2px solid #dc3545;">
        </div>

        <!-- Formulario debajo de la imagen -->
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <input id="email" type="email" 
                           class="form-control bg-light text-dark @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus
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

                <!-- Forgot password -->
                <div class="mb-3 text-end">
                    <a href="{{ route('password.request') }}" class="text-decoration-none text-primary">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón de login -->
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-danger btn-sm">Iniciar sesión</button>
                </div>
            </form>

            <!-- Register link -->
            <div class="text-center mt-2">
                <p class="mb-1">¿No tenés cuenta?</p>
                <a href="{{ route('register') }}" class="btn btn-outline-dark btn-sm">
                    Registrarse
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
