<table class="table table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Cliente</th>
            <th>Barbero</th>
            <th>Servicio</th>
            <th>Sucursal</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($turnos as $turno)
        <tr>
            <td>{{ $turno->cliente?->nombre ?? '-' }}</td>
            <td>{{ $turno->barbero?->nombre ?? '-' }}</td>
            <td>{{ $turno->servicio?->nombre ?? '-' }}</td>
            <td>{{ $turno->sucursal?->nombre ?? '-' }}</td>
            <td>{{ $turno->fecha }}</td>
            <td>{{ $turno->hora }}</td>
            <td>{{ ucfirst($turno->estado) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
