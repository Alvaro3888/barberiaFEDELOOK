@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Listado de usuarios</h2>

    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary mb-3">Nuevo usuario</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Rol</th>
                <th>Sucursal</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->telefono ?? '-' }}</td>
                    <td>{{ $usuario->rol ? $usuario->rol->nombre : 'Sin rol' }}</td>
                    <td>{{ $usuario->sucursal ? $usuario->sucursal->nombre : 'Sin sucursal' }}</td>
                    <td>
                        @if($usuario->activo)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        @if($usuario->activo)
                            <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Seguro que deseas desactivar este usuario?')">
                                    Desactivar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.usuarios.activate', $usuario->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('¿Seguro que deseas activar este usuario?')">
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
