@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Listado de sucursales</h2>

    <a href="{{ route('admin.sucursales.create') }}" class="btn btn-primary mb-3">Nueva sucursal</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Dirección</th>
                <th>Teléfono</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->nombre }}</td>
                    <td>{{ $sucursal->direccion }}</td>
                    <td>{{ $sucursal->telefono }}</td>
                    <td>{{ $sucursal->activo ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.sucursales.edit', $sucursal->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        @if($sucursal->activo)
                            <form action="{{ route('admin.sucursales.destroy', $sucursal->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Seguro que deseas desactivar esta sucursal?')">
                                    Desactivar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.sucursales.activate', $sucursal->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('¿Seguro que deseas activar esta sucursal?')">
                                    Activar
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
