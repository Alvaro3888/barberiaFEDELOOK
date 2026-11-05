@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Editar Tipo de Servicio</h2>

    <form action="{{ route('admin.TipoServicios.update', $tipo->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- 🔹 Selección de sucursal -->
        <div class="mb-3">
            <label for="sucursal_id" class="form-label">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-select" required>
                <option value="">Seleccione una sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" 
                        {{ old('sucursal_id', $tipo->sucursal_id) == $sucursal->id ? 'selected' : '' }}>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- 🔹 Campos deshabilitados hasta elegir sucursal -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" 
                   value="{{ old('nombre', $tipo->nombre) }}" disabled required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" id="descripcion" class="form-control" disabled>{{ old('descripcion', $tipo->descripcion) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success" id="btn-submit" disabled>Actualizar</button>
        <a href="{{ route('admin.TipoServicios.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sucursalSel = document.getElementById('sucursal_id');
    const campos      = ['nombre','descripcion','btn-submit'];

    function toggleCampos() {
        const habilitar = !!sucursalSel.value;
        campos.forEach(id => document.getElementById(id).disabled = !habilitar);
    }

    // Inicializar al cargar la página
    toggleCampos();

    // Reaccionar al cambio de sucursal
    sucursalSel.addEventListener('change', toggleCampos);
});
</script>
@endsection
