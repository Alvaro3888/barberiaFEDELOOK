<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Sucursal</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($usuarios as $usuario)
        <tr>
            <td>{{ $usuario->nombre }}</td>
            <td>{{ $usuario->email }}</td>
            <td>{{ $usuario->telefono ?? '-' }}</td>
            <td>{{ $usuario->rol?->nombre ?? 'Sin rol' }}</td>
            <td>{{ $usuario->sucursal?->nombre ?? 'Sin sucursal' }}</td>
            <td>
                <!-- Editar -->
                <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" 
                   class="btn btn-sm btn-warning">
                   Editar
                </a>

                <!-- Eliminar -->
                <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" 
                      method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

