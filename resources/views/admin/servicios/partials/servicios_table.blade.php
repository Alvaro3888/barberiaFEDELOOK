<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Nombre</th>
            <th>Duración</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Sucursal</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($servicios as $servicio)
        <tr>
            <td>{{ $servicio->nombre }}</td>
            <td>{{ $servicio->duracion }} min</td>
            <td>${{ $servicio->precio_actual }}</td>
            <td>{{ $servicio->categoria }}</td>
            <td>{{ $servicio->sucursal?->nombre ?? '-' }}</td>
            <td>
                <a href="{{ route('admin.servicios.edit',$servicio->id) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('admin.servicios.destroy',$servicio->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
