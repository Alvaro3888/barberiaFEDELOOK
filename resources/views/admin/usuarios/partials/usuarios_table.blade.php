<table class="table table-hover align-middle mb-0" id="tablaUsuarios">
    <thead class="bg-light text-uppercase font-weight-bold text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
        <tr>
            <th class="py-3 px-4">Nombre</th>
            <th class="py-3">Email</th>
            <th class="py-3">Teléfono</th>
            <th class="py-3">Estado</th>
            <th class="py-3 px-4 text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($usuarios as $usuario)
        <tr class="fila-usuario transition-row">
            <td class="nombre-usuario px-4 py-3">
                <div class="font-weight-bold text-dark">{{ $usuario->nombre }}</div>
            </td>
            <td class="email-usuario py-3 text-dark">{{ $usuario->email }}</td>
            <td class="py-3 text-secondary">{{ $usuario->telefono ?? 'Sin teléfono' }}</td>
            <td class="py-3">
                <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }} text-white px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </td>
            <td class="px-4 py-3 text-center">
                <div class="d-flex justify-content-center align-items-center gap-2">
                    <!-- Editar -->
                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn-action btn-action-info" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                    </a>

                    <!-- Desactivar / Activar (o Eliminar según tu lógica de controlador) -->
                    @if($usuario->activo)
                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Desactivar este cliente?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-danger" title="Desactivar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.usuarios.activate', $usuario->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-action btn-action-success" title="Activar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr id="sinResultados">
            <td colspan="5" class="text-center py-5 text-muted">
                <div class="py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-people text-muted opacity-50 mb-3" viewBox="0 0 16 16"><path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.382 10 11 10c1.618 0 2.688.629 3.24 1.276.593.69.758 1.456.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a5.88 5.88 0 0 0-1.237-.407C5.325 8.827 4.793 8 3 8c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                    <p class="mb-0 font-weight-medium">No se encontraron clientes registrados en el sistema.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>