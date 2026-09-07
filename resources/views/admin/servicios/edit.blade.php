@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar servicio</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.servicios.update', $servicio->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control"
                   value="{{ $servicio->nombre }}" required>
        </div>

        <div class="mb-3">
            <label for="duracion" class="form-label">Duración (minutos)</label>
            <input type="number" name="duracion" class="form-control"
                   value="{{ $servicio->duracion }}" required>
        </div>

        <div class="mb-3">
            <label for="precio_actual" class="form-label">Precio</label>
            <input type="number" step="0.01" name="precio_actual" class="form-control"
                   value="{{ $servicio->precio_actual }}" required>
        </div>

        <div class="mb-3">
            <label for="categoria" class="form-label">Categoría</label>
            <input type="text" name="categoria" class="form-control"
                   value="{{ $servicio->categoria }}" required>
        </div>

        <div class="mb-3">
            <label for="sucursal_id" class="form-label">Sucursal</label>
            <select name="sucursal_id" class="form-select" required>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}"
                        {{ $servicio->sucursal_id == $sucursal->id ? 'selected' : '' }}>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" name="imagen" class="form-control">
            @if($servicio->imagen)
                <p class="mt-2">Imagen actual:</p>
                <img src="{{ asset('storage/'.$servicio->imagen) }}" alt="Imagen servicio" width="150">
            @endif
        </div>

        <button type="submit" class="btn btn-success">Actualizar servicio</button>
        <a href="{{ route('admin.servicios.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
