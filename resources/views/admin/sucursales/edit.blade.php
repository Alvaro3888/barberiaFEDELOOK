@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 text-primary text-center">Editar Sucursal</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sucursales.update', $sucursal->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $sucursal->nombre) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="{{ old('direccion', $sucursal->direccion) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="{{ old('telefono', $sucursal->telefono) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="horarios" class="form-label">Horarios</label>
            <input type="text" name="horarios" id="horarios" value="{{ old('horarios', $sucursal->horarios) }}" class="form-control">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success">Guardar cambios</button>
