@extends('layouts.public')

@section('content')
<div class="container my-5 bg-paper border-ink p-4 rounded">

    <h2 class="section-title">Editar turno</h2>

    {{-- Mensajes de error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-charcoal">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuario.turnos.update', $turno->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Sucursal -->
        <div class="mb-3">
            <label for="sucursal_id" class="form-label text-ink">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-select" required>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" {{ $turno->sucursal_id == $sucursal->id ? 'selected' : '' }}>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Servicio -->
        <div class="mb-3">
            <label for="servicio_id" class="form-label text-ink">Servicio</label>
            <select name="servicio_id" id="servicio_id" class="form-select" required>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}" {{ $turno->servicio_id == $servicio->id ? 'selected' : '' }}>
                        {{ $servicio->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Barbero -->
        <div class="mb-3">
            <label for="barbero_id" class="form-label text-ink">Barbero</label>
            <select name="barbero_id" id="barbero_id" class="form-select" required>
                @foreach($barberos as $barbero)
                    <option value="{{ $barbero->id }}" {{ $turno->barbero_id == $barbero->id ? 'selected' : '' }}>
                        {{ $barbero->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Fecha -->
        <div class="mb-3">
            <label for="fecha" class="form-label text-ink">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ $turno->fecha }}" required>
        </div>

        <!-- Hora -->
        <div class="mb-3">
            <label for="hora" class="form-label text-ink">Hora</label>
            <input type="time" name="hora" id="hora" class="form-control" value="{{ $turno->hora }}" required>
        </div>

        <!-- Botones -->
        <button type="submit" class="btn btn-oxblood fw-bold">Actualizar turno</button>
        <a href="{{ route('usuario.turnos') }}" class="btn btn-outline-ink">Volver</a>
    </form>
</div>
@endsection
