@extends('layouts.guest')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4 my-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold">Recuperar Contraseña</h3>
                <p class="text-muted small">
                    ¿Olvidaste tu contraseña? No hay problema. Solo indicanos tu correo electrónico y te enviaremos un enlace de restablecimiento.
                </p>
            </div>

            <!-- Session Status (Mensaje de éxito dinámico) -->
            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-medium">Correo Electrónico</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="tucorreo@email.com">
                    
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-dark btn-lg py-3 rounded-3 fw-bold shadow-sm">
                        Enviar enlace de restablecimiento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection