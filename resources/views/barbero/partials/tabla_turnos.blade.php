<table class="table table-striped text-center align-middle mb-4">
    <thead class="table-light">
        <tr>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Sucursal</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($lista as $turno)
        <tr>
            <td>{{ $turno->cliente->name }}</td>
            <td>{{ $turno->servicio->nombre }}</td>
            <td>{{ $turno->sucursal->nombre }}</td>
            <td>{{ $turno->hora }}</td>
            <td>{{ ucfirst($turno->estado) }}</td>
            <td>
                @if($turno->estado == 'pendiente')
                    <form action="{{ route('admin.turnos.update', $turno->id) }}" method="POST" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="confirmado">
                        <button type="submit" class="btn btn-primary btn-sm">Confirmar</button>
                    </form>
                @endif
                @if($turno->estado == 'confirmado')
                    <form action="{{ route('admin.turnos.update', $turno->id) }}" method="POST" class="d-inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="estado" value="completado">
                        <button type="submit" class="btn btn-success btn-sm">Completado</button>
                    </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
