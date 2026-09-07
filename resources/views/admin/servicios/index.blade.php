@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Listado de servicios</h2>

    <a href="{{ route('admin.servicios.create') }}" class="btn btn-primary mb-3">Nuevo servicio</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Duración</th>
                <th>Precio</th>
                <th>Categoría</th>
                <th>Sucursal</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $servicio)
                <tr>
                    <td>{{ $servicio->nombre }}</td>
                    <td>{{ $servicio->duracion }} min</td>
                    <td>${{ $servicio->precio_actual }}</td>
                    <td>{{ $servicio->categoria }}</td>
                    <td>{{ $servicio->sucursal ? $servicio->sucursal->nombre : 'Sin sucursal' }}</td>
                    <td>{{ $servicio->activo ? 'Sí' : 'No' }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <!-- Editar -->
                            <a href="{{ route('admin.servicios.edit', $servicio->id) }}" 
                               class="btn btn-sm btn-warning">
                                Editar
                            </a>

                            <!-- Activar / Desactivar -->
                            @if($servicio->activo)
                                <form action="{{ route('admin.servicios.destroy', $servicio->id) }}" 
                                      method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Seguro que deseas desactivar este servicio?')">
                                        Desactivar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.servicios.activate', $servicio->id) }}" 
                                      method="POST" style="display:inline-block">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('¿Seguro que deseas activar este servicio?')">
                                        Activar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
