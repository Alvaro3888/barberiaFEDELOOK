@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f4f7f6; min-height: 100vh;">
    <!-- Encabezado -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom animate-fade-in">
        <div class="mb-3 mb-md-0">
            <h1 class="h4 font-weight-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span class="p-2 text-white rounded-3 shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.7A6.025 6.025 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    </svg>
                </span>
                Gestión de Personal & Operativa
            </h1>
            <p class="text-muted small mb-0">Controla al equipo de trabajo y configura dinámicamente las franjas horarias de la barbería.</p>
        </div>
        <a href="{{ route('admin.personal.create') }}" class="btn px-4 py-2 font-weight-bold shadow-sm d-inline-flex align-items-center gap-2 btn-video-glow rounded-pill text-white text-nowrap" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); border: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
            </svg>
            Registrar Nuevo Personal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center animate-fade-in" role="alert" style="background-color: #d1fae5; color: #065f46;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div><span>{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bloque 1: Configuración de Horarios -->
    @if(isset($turnos) && $turnos->count() > 0)
        <div class="p-4 rounded-4 mb-4 shadow-sm border bg-white">
            <div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom">
                <span class="p-2 text-white rounded-3 shadow-sm" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                    </svg>
                </span>
                <div>
                    <h5 class="font-weight-bold mb-0 text-dark fs-6">Configuración Dinámica de Franjas Horarias</h5>
                    <p class="text-muted small mb-0">Modifica las horas y nombres de los turnos de forma rápida y segura.</p>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($turnos as $turno)
                    <div class="col-md-4">
                        <form action="{{ route('admin.personal.updateTurnoHorario', $turno->id) }}" method="POST" class="p-4 rounded-4 shadow-xs turno-card h-100 d-flex flex-column justify-content-between bg-light border form-turno-item" novalidate>
                            @csrf
                            @method('PUT')
                            
                            <div>
                                <div class="mb-3">
                                    <label class="form-label text-uppercase font-weight-bold text-secondary small" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nombre del Turno <span class="text-danger">*</span></label>
                                    <!-- Input blindado contra números -->
                                    <input type="text" name="nombre" class="form-control border bg-white py-2 input-solo-letras" value="{{ $turno->nombre }}" required autocomplete="off">
                                    <div class="invalid-feedback small">Este campo solo permite letras y espacios.</div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label text-muted small" style="font-size: 0.75rem;">Apertura <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_inicio" class="form-control border bg-white py-2 hora-inicio" value="{{ substr($turno->hora_inicio, 0, 5) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small" style="font-size: 0.75rem;">Cierre <span class="text-danger">*</span></label>
                                        <input type="time" name="hora_fin" class="form-control border bg-white py-2 hora-fin" value="{{ substr($turno->hora_fin, 0, 5) }}" required>
                                        <div class="invalid-feedback small">Cierre inválido.</div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm w-100 py-2 font-weight-bold shadow-sm btn-video-action rounded-pill text-white mt-2" style="background: #0f172a; border: none;">
                                Actualizar Horario
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Bloque 2: Tabla Principal de Personal -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden card-modern bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase font-weight-bold text-muted border-bottom" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="py-3 px-4">Colaborador / Rol</th>
                            <th class="py-3">Contacto</th>
                            <th class="py-3">Sucursal Asignada</th>
                            <th class="py-3">Franja Horaria / Turno</th>
                            <th class="py-3 px-4 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barberos as $personal)
                            <tr class="{{ !$personal->activo ? 'table-secondary text-muted opacity-75' : '' }} transition-row">
                                <td class="px-4 py-3">
                                    <div class="font-weight-bold text-dark">{{ $personal->nombre }}</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        @if($personal->rol_id == 1)
                                            <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill shadow-xs" style="font-size: 0.65rem;">Administrador</span>
                                        @else
                                            <span class="badge bg-dark text-white px-2.5 py-1 rounded-pill shadow-xs" style="font-size: 0.65rem;">Barbero</span>
                                        @endif

                                        @if(!$personal->activo)
                                            <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">Inactivo</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="text-dark font-weight-medium small">{{ $personal->email }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $personal->telefono ?? 'Sin teléfono' }}</small>
                                </td>
                                <form action="{{ route('admin.personal.updateTurno', $personal->id) }}" method="POST" id="form-turno-{{ $personal->id }}">
                                    @csrf
                                    @method('PUT')
                                    <td class="py-3">
                                        <select name="sucursal_id" class="form-select form-select-sm border bg-light shadow-xs py-2" {{ !$personal->activo ? 'disabled' : 'required' }}>
                                            <option value="">Sin sucursal</option>
                                            @foreach($sucursales as $sucursal)
                                                <option value="{{ $sucursal->id }}" {{ $personal->sucursal_id == $sucursal->id ? 'selected' : '' }}>
                                                    {{ $sucursal->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-3">
                                        <select name="turno_trabajo_id" class="form-select form-select-sm border bg-light shadow-xs py-2" {{ !$personal->activo ? 'disabled' : '' }}>
                                            <option value="">Sin turno fijo</option>
                                            @if(isset($turnos))
                                                @foreach($turnos as $turno)
                                                    <option value="{{ $turno->id }}" {{ $personal->turno_trabajo_id == $turno->id ? 'selected' : '' }}>
                                                        {{ $turno->nombre }} ({{ substr($turno->hora_inicio, 0, 5) }} - {{ substr($turno->hora_fin, 0, 5) }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </td>
                                </form>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-2">
                                        @if($personal->activo)
                                            <button type="submit" form="form-turno-{{ $personal->id }}" class="btn-action btn-action-primary" title="Guardar cambios">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M11 2H9v3h2z"/>
                                                    <path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.414 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0M1 1v13.5a.5.5 0 0 0 .5.5H3v-7a1.5 1.5 0 0 1 1.5-1.5h7A1.5 1.5 0 0 1 13 8v7h1.5a.5.5 0 0 0 .5-.5V3.086a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H12v3.5A1.5 1.5 0 0 1 10.5 6h-7A1.5 1.5 0 0 1 2 4.5V1z"/>
                                                </svg>
                                            </button>

                                            <a href="{{ route('admin.personal.edit', $personal->id) }}" class="btn-action btn-action-info" title="Editar perfil">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                                </svg>
                                            </a>
                                        @endif

                                        @if($personal->activo)
                                            <form action="{{ route('admin.personal.destroy', $personal->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de desactivar a este miembro del personal?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-danger" title="Desactivar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.personal.activate', $personal->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn-action btn-action-success" title="Activar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-people text-muted opacity-50 mb-3" viewBox="0 0 16 16">
                                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.382 10 11 10c1.618 0 2.688.629 3.24 1.276.593.69.758 1.456.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a5.88 5.88 0 0 0-1.237-.407C5.325 8.827 4.793 8 3 8c-4 0-5 3-5 4s1 1 1 1h4.216zM4.5 7.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                        </svg>
                                        <p class="mb-0 font-weight-medium small">No hay personal registrado en el sistema.</p>
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

<!-- JavaScript para bloquear números estrictamente por tecla y limpiar al pegar -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputsTexto = document.querySelectorAll('.input-solo-letras');

        inputsTexto.forEach(input => {
            // Bloquea números directamente al presionar la tecla (key press)
            input.addEventListener('keypress', function(e) {
                const charCode = e.which ? e.which : e.keyCode;
                // Permite letras (mayúsculas y minúsculas), tildes, eñes y espacios (códigos ASCII comunes y extendidos)
                const charStr = String.fromCharCode(charCode);
                const regexValido = /^[A-Za-zÀ-ÿ\s]$/;

                if (!regexValido.test(charStr)) {
                    e.preventDefault(); // Cancela la pulsación si es número u otro símbolo
                }
            });

            // Limpia automáticamente si intentan pegar texto con números (paste)
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
                const textoLimpio = textoPegado.replace(/[^A-Za-zÀ-ÿ\s]/g, '');
                
                // Inserta el texto limpio en la posición del cursor
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
                this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
            });

            // Validación visual de apoyo
            input.addEventListener('input', function() {
                if (this.value.trim().length === 0) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        });

        // Validación general de formularios de turnos antes de enviar
        const formulariosTurnos = document.querySelectorAll('.form-turno-item');
        formulariosTurnos.forEach(form => {
            form.addEventListener('submit', function(e) {
                const inputNombre = form.querySelector('.input-solo-letras');
                const horaInicio = form.querySelector('.hora-inicio');
                const horaFin = form.querySelector('.hora-fin');

                const regexLetras = /^[A-Za-zÀ-ÿ\s]+$/;
                if (inputNombre && !regexLetras.test(inputNombre.value.trim())) {
                    e.preventDefault();
                    inputNombre.classList.add('is-invalid');
                    inputNombre.focus();
                    return;
                }

                if (horaInicio && horaFin && horaFin.value <= horaInicio.value) {
                    e.preventDefault();
                    horaFin.classList.add('is-invalid');
                    horaFin.focus();
                    return;
                }
            });
        });
    });
</script>

<style>
    .card-modern {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important;
        border: none;
    }
    .turno-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0 !important;
    }
    .turno-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(13, 148, 136, 0.1) !important;
        border-color: #0d9488 !important;
    }
    .btn-video-glow {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3);
    }
    .btn-video-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(13, 148, 136, 0.5);
    }
    .btn-video-action {
        transition: all 0.3s ease;
    }
    .btn-video-action:hover {
        transform: translateY(-1px);
        background-color: #0d9488 !important;
    }
    .transition-row {
        transition: all 0.2s ease;
    }
    .transition-row:hover {
        background-color: #f8fafc !important;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        background: #f8f9fa;
        cursor: pointer;
    }
    .btn-action-primary {
        color: #0d6efd;
        border-color: #cfe2ff;
        background: #f0f7ff;
    }
    .btn-action-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }
    .btn-action-info {
        color: #0dcaf0;
        border-color: #cff4fc;
        background: #eefcfd;
    }
    .btn-action-info:hover {
        background-color: #0dcaf0;
        color: #000;
    }
    .btn-action-danger {
        color: #dc3545;
        border-color: #f8d7da;
        background: #fef2f2;
    }
    .btn-action-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }
    .btn-action-success {
        color: #198754;
        border-color: #d1e7dd;
        background: #edf7f2;
    }
    .btn-action-success:hover {
        background-color: #198754;
        color: #fff;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endsection