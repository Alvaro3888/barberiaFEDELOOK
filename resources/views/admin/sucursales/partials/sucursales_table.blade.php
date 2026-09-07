<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sucursales as $sucursal)
        <tr>
            <td>{{ $sucursal->nombre }}</td>
            <td>{{ $sucursal->direccion }}</td>
            <td>{{ $sucursal->telefono ?? '-' }}</td>
            <td>
                <a href="{{ route('sucursales.edit',$sucursal->id) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('sucursales.destroy',$sucursal->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
