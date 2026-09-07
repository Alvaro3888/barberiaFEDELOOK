@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary text-center">Reservar Turno</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('turnos.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="sucursal_id" class="form-label">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-select" required>
                <option value="">Seleccione una sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="barbero_id" class="form-label">Barbero</label>
            <select name="barbero_id" id="barbero_id" class="form-select" required>
                <option value="">Seleccione un barbero</option>
                {{-- Se llenará dinámicamente según la sucursal elegida --}}
            </select>
        </div>

        <div class="mb-3">
            <label for="servicio_id" class="form-label">Servicio</label>
            <select name="servicio_id" id="servicio_id" class="form-select" required>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }} - ${{ $servicio->precio_actual }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" name="hora" id="hora" class="form-control" required>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Reservar</button>
            <a href="{{ route('index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

{{-- Script para cargar barberos según sucursal --}}
<script>
document.getElementById('sucursal_id').addEventListener('change', function() {
    let sucursalId = this.value;
    let barberoSelect = document.getElementById('barbero_id');
    barberoSelect.innerHTML = '<option value="">Cargando...</option>';

    if(sucursalId) {
        fetch('/api/barberos/' + sucursalId)
            .then(response => response.json())
            .then(data => {
                barberoSelect.innerHTML = '<option value="">Seleccione un barbero</option>';
                data.forEach(barbero => {
                    barberoSelect.innerHTML += `<option value="${barbero.id}">${barbero.nombre}</option>`;
                });
            });
    } else {
        barberoSelect.innerHTML = '<option value="">Seleccione un barbero</option>';
    }
});
</script>
@endsection
