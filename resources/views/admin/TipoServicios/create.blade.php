@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Nuevo Tipo de Servicio</h2>

    <form action="{{ route('admin.TipoServicios.store') }}" method="POST">
        @csrf

        <!-- 🔹 Selección de sucursal -->
        <div class="mb-3">
            <label for="sucursal_id" class="form-label">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-select" required>
                <option value="">Seleccione una sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" {{ old('sucursal_id') == $sucursal->id ? 'selected' : '' }}>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 🔹 Campos deshabilitados hasta elegir sucursal -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}" disabled required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" disabled>{{ old('descripcion') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success" id="btn-submit" disabled>Guardar</button>
        <a href="{{ route('admin.TipoServicios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sucursalSel = document.getElementById('sucursal_id');
    const campos      = ['nombre','descripcion','btn-submit'];

    sucursalSel.addEventListener('change', function() {
        const habilitar = !!this.value;
        campos.forEach(id => document.getElementById(id).disabled = !habilitar);
    });
});
</script>
@endsection
