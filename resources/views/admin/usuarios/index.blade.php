@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 pb-3 border-bottom animate-fade-in">
        <div class="mb-3 mb-md-0">
            <h1 class="h3 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="p-2 text-white rounded-3 shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.7A6.025 6.025 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </span>
                Gestión de Clientes
            </h1>
            <p class="text-muted mb-0">Visualiza y administra únicamente a los clientes finales registrados en la plataforma.</p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" class="btn px-4 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center gap-2 btn-video-glow rounded-3 text-white" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
            </svg>
            Nuevo Cliente
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 alert-dismissible fade show" role="alert" style="background-color: #d1fae5; color: #065f46;">
            <div class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Barra de Búsqueda Exclusiva para Clientes -->
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-4 bg-white">
        <div class="row align-items-center">
            <div class="col-md-10 mb-2 mb-md-0">
                <label for="buscar" class="form-label small fw-bold text-uppercase text-muted">Buscar Cliente</label>
                <input type="text" id="buscar" class="form-control form-control-lg border bg-light py-2 fs-6" 
                       value="{{ request('buscar') }}" placeholder="Escribe el nombre o correo del cliente..." autocomplete="off">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light btn-lg w-100 py-2 border text-secondary font-weight-bold btn-hover-scale fs-6">Limpiar</a>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tablaUsuarios">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted border-bottom" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-4">Cliente</th>
                            <th class="py-3">Correo Electrónico</th>
                            <th class="py-3">Teléfono</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
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
                                    <a href="{{ route('admin.usuarios.edit', $usuario->id) }}" class="btn-action btn-action-info" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                                    </a>

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
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputBuscar = document.getElementById('buscar');
    const filasUsuarios = document.querySelectorAll('.fila-usuario');

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase().trim();

            filasUsuarios.forEach(fila => {
                const nombre = fila.querySelector('.nombre-usuario').textContent.toLowerCase();
                const email = fila.querySelector('.email-usuario').textContent.toLowerCase();

                if (nombre.includes(textoBusqueda) || email.includes(textoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }
});
</script>

<style>
    .card-modern {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
        border: none;
    }
    .transition-row {
        transition: background-color 0.2s ease;
    }
    .transition-row:hover {
        background-color: #fafbfd !important;
    }
    .btn-hover-scale {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .btn-hover-scale:hover {
        transform: translateY(-1px);
        background-color: #e2e8f0 !important;
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.5);
    }
    .btn-action {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.2s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: #f1f3f5;
        cursor: pointer;
    }
    .btn-action-info {
        color: #0dcaf0;
        border-color: #e0fcfd;
        background: #eefcfd;
    }
    .btn-action-info:hover {
        background-color: #0dcaf0;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(13, 202, 240, 0.3);
    }
    .btn-action-danger {
        color: #dc3545;
        border-color: #fde8e8;
        background: #fef2f2;
    }
    .btn-action-danger:hover {
        background-color: #dc3545;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }
    .btn-action-success {
        color: #198754;
        border-color: #e3f8ec;
        background: #edf7f2;
    }
    .btn-action-success:hover {
        background-color: #198754;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
    }
    .form-control:focus {
        border-color: #0d9488 !important;
        box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1) !important;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endsection