@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 py-md-5 px-lg-4" style="background-color: #f4f7f6; min-height: 100vh;">
    
    <!-- Encabezado 2.0 -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom animate-fade-in gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="p-2 text-white rounded-3 shadow-sm brand-icon-container btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.7A6.025 6.025 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                </svg>
            </span>
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Gestión de Clientes</h1>
                <p class="text-muted mb-0 small">Base de datos operativa, contactos y control de clientes frecuentes.</p>
            </div>
        </div>
        <button type="button" class="btn px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 btn-video-glow rounded-pill text-white border-0 btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);" data-bs-toggle="modal" data-bs-target="#modalCrearCliente">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
            </svg>
            Nuevo Cliente
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 alert-dismissible fade show d-flex align-items-center animate-fade-in" role="alert" style="background-color: #E8F5F3; color: #0F746E;">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div><span>{{ session('success') }}</span></div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- KPIs / Tarjetas Resumen -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white card-modern">
                <span class="text-muted small text-uppercase fw-bold">Total Registrados</span>
                <h3 class="fw-bold text-dark mb-0 mt-1">{{ $usuarios->count() }}</h3>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white card-modern">
                <span class="text-muted small text-uppercase fw-bold">Activos</span>
                <h3 class="fw-bold text-teal mb-0 mt-1" style="color: #0d9488;">{{ $usuarios->where('activo', true)->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="card p-3 p-md-4 mb-4 shadow-sm border-0 rounded-4 bg-white card-modern">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-10">
                <input type="text" id="buscar" class="form-control form-control-lg border bg-light py-2 fs-6 rounded-3 shadow-none" 
                       value="{{ request('buscar') }}" placeholder="🔍 Buscar por nombre, correo o teléfono..." autocomplete="off">
            </div>
            <div class="col-12 col-md-2">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light btn-lg w-100 py-2 border text-secondary fw-bold fs-6 rounded-3 btn-no-select">Limpiar</a>
            </div>
        </div>
    </div>

    <!-- ================= VISTA DESKTOP (TABLA CON SCROLL Y STICKY HEADER) ================= -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden card-modern d-none d-md-block bg-white">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase fw-bold text-muted border-bottom sticky-top shadow-xs" style="font-size: 0.7rem; letter-spacing: 0.5px; z-index: 5; background-color: #f8f9fa !important;">
                        <tr>
                            <th class="py-3 ps-4 bg-light">Cliente</th>
                            <th class="py-3 bg-light">Correo Electrónico</th>
                            <th class="py-3 bg-light">Teléfono / WhatsApp</th>
                            <th class="py-3 text-center bg-light">Estado</th>
                            <th class="py-3 pe-4 text-end bg-light">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr class="fila-usuario transition-row">
                            <td class="nombre-usuario ps-4 py-3 fw-bold text-dark">{{ $usuario->nombre }}</td>
                            <td class="email-usuario py-3 text-dark">{{ $usuario->email }}</td>
                            <td class="telefono-usuario py-3 text-secondary">
                                @if($usuario->telefono)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $usuario->telefono) }}" target="_blank" class="text-decoration-none text-success fw-bold d-inline-flex align-items-center gap-1" title="Abrir WhatsApp">
                                        💬 {{ $usuario->telefono }}
                                    </a>
                                @else
                                    <span class="text-muted fst-italic">Sin teléfono</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }} text-white px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.68rem;">
                                    {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <!-- Botón Editar abre modal dinámico -->
                                    <button type="button" class="btn-action btn-action-info btn-editar btn-no-select" 
                                            data-id="{{ $usuario->id }}" 
                                            data-nombre="{{ $usuario->nombre }}" 
                                            data-email="{{ $usuario->email }}" 
                                            data-telefono="{{ $usuario->telefono }}" 
                                            data-activo="{{ $usuario->activo }}"
                                            title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/></svg>
                                    </button>

                                    @if($usuario->activo)
                                        <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Desactivar este cliente?');" class="d-inline form-action-prevent">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-danger btn-with-scissors btn-no-select" title="Desactivar">
                                                <span class="btn-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/></svg>
                                                </span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.usuarios.activate', $usuario->id) }}" method="POST" class="d-inline form-action-prevent">
                                            @csrf 
                                            <button type="submit" class="btn-action btn-action-success btn-with-scissors btn-no-select" title="Activar">
                                                <span class="btn-text">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/></svg>
                                                </span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <span class="fs-2 d-block mb-2">📋</span>
                                    <p class="mb-0 fw-medium">No se encontraron clientes registrados.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= VISTA MÓVIL (TARJETAS TÁCTILES) ================= -->
    <div class="d-block d-md-none">
        <div class="row g-3">
            @forelse($usuarios as $usuario)
            <div class="col-12 fila-usuario">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white card-modern">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-0 nombre-usuario">{{ $usuario->nombre }}</h5>
                            <span class="email-usuario text-muted small">{{ $usuario->email }}</span>
                        </div>
                        <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }} text-white px-2.5 py-1 rounded-pill btn-no-select" style="font-size: 0.65rem;">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    
                    <div class="mb-3 small text-secondary">
                        <strong>Tel:</strong> 
                        <span class="telefono-usuario">
                            @if($usuario->telefono)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $usuario->telefono) }}" target="_blank" class="text-success fw-bold text-decoration-none">
                                    💬 {{ $usuario->telefono }}
                                </a>
                            @else
                                <span class="text-muted fst-italic">Sin teléfono</span>
                            @endif
                        </span>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-2">
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold btn-editar btn-no-select" 
                                style="font-size: 0.75rem;"
                                data-id="{{ $usuario->id }}" 
                                data-nombre="{{ $usuario->nombre }}" 
                                data-email="{{ $usuario->email }}" 
                                data-telefono="{{ $usuario->telefono }}" 
                                data-activo="{{ $usuario->activo }}">
                            Editar
                        </button>
                        
                        @if($usuario->activo)
                            <form action="{{ route('admin.usuarios.destroy', $usuario->id) }}" method="POST" onsubmit="return confirm('¿Desactivar?');" class="d-inline form-action-prevent">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold btn-with-scissors btn-no-select" style="font-size: 0.75rem;"><span class="btn-text">Desactivar</span></button>
                            </form>
                        @else
                            <form action="{{ route('admin.usuarios.activate', $usuario->id) }}" method="POST" class="d-inline form-action-prevent">
                                @csrf 
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold btn-with-scissors btn-no-select" style="font-size: 0.75rem;"><span class="btn-text">Activar</span></button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-4 text-muted bg-white rounded-4 shadow-sm">
                <span class="fs-2 d-block mb-2">📋</span>
                <p class="mb-0">No se encontraron clientes registrados.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL CREAR CLIENTE ================= -->
<div class="modal fade" id="modalCrearCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                <h5 class="fw-bold mb-0">Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="form-action-prevent" novalidate>
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre_crear" class="form-control rounded-3 shadow-none" required placeholder="Ej: Juan Pérez" autocomplete="off">
                        <div class="invalid-feedback">El nombre solo debe contener letras y espacios.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3 shadow-none" required placeholder="correo@ejemplo.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" class="form-control rounded-3 shadow-none" placeholder="Ej: 388... (Opcional)">
                    </div>
                    <div class="alert alert-light border small text-muted mb-0 rounded-3">
                        ℹ️ No se solicita contraseña. El sistema genera una credencial segura interna. El cliente podrá pedir acceso mediante el botón de recuperar contraseña si desea ingresar al portal web.
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-no-select" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold rounded-pill px-4 border-0 btn-video-glow btn-with-scissors btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                        <span class="btn-text">Guardar Cliente</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDITAR CLIENTE ================= -->
<div class="modal fade" id="modalEditarCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="fw-bold mb-0">Editar Datos del Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarCliente" method="POST" class="form-action-prevent" novalidate>
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control rounded-3 shadow-none" required autocomplete="off">
                        <div class="invalid-feedback">El nombre solo debe contener letras y espacios.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3 shadow-none" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Teléfono / WhatsApp</label>
                        <input type="text" name="telefono" id="edit_telefono" class="form-control rounded-3 shadow-none">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-secondary">Estado</label>
                        <select name="activo" id="edit_activo" class="form-select rounded-3 shadow-none">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 btn-no-select" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white fw-bold rounded-pill px-4 border-0 btn-video-glow btn-with-scissors btn-no-select" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%);">
                        <span class="btn-text">Actualizar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= SCRIPTS DE FILTRADO, VALIDACIÓN Y MODAL ================= -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro instantáneo en vivo (Nombre, Email y Teléfono)
    const inputBuscar = document.getElementById('buscar');
    const filasUsuarios = document.querySelectorAll('.fila-usuario');

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            const textoBusqueda = this.value.toLowerCase().trim();
            filasUsuarios.forEach(fila => {
                const nombreEl = fila.querySelector('.nombre-usuario');
                const emailEl = fila.querySelector('.email-usuario');
                const telefonoEl = fila.querySelector('.telefono-usuario');

                const nombre = nombreEl ? nombreEl.textContent.toLowerCase() : '';
                const email = emailEl ? emailEl.textContent.toLowerCase() : '';
                const telefono = telefonoEl ? telefonoEl.textContent.toLowerCase() : '';

                if (nombre.includes(textoBusqueda) || email.includes(textoBusqueda) || telefono.includes(textoBusqueda)) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        });
    }

    // Prevenir selección de texto accidental en botones
    document.querySelectorAll('.btn-no-select').forEach(btn => {
        btn.addEventListener('mousedown', function (e) {
            if (e.detail > 1) e.preventDefault();
        });
    });

    // SISTEMA ANTI-DOBLE CLIC Y SPINNER DE TIJERAS INTELIGENTE (Retraso de 250ms)
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.classList.contains('form-action-prevent')) {
            const submitBtn = form.querySelector('.btn-with-scissors');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const textSpan = submitBtn.querySelector('.btn-text');
                if (textSpan) {
                    textSpan.dataset.original = textSpan.innerHTML;
                    
                    let spinnerTimer = setTimeout(() => {
                        textSpan.innerHTML = `<i class="bi bi-scissors scissors-loader me-1"></i> Procesando...`;
                    }, 250);

                    window.addEventListener('beforeunload', () => clearTimeout(spinnerTimer));
                }
            }
        }
    });

    // Función auxiliar para blindar inputs de nombres contra números
    function blindarSoloLetras(elemento) {
        if (!elemento) return;

        elemento.addEventListener('keypress', function(e) {
            const charStr = String.fromCharCode(e.which ? e.which : e.keyCode);
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]$/.test(charStr)) {
                e.preventDefault();
            }
        });

        elemento.addEventListener('paste', function(e) {
            e.preventDefault();
            const textoPegado = (e.clipboardData || window.clipboardData).getData('text');
            const textoLimpio = textoPegado.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.substring(0, start) + textoLimpio + this.value.substring(end);
            this.setSelectionRange(start + textoLimpio.length, start + textoLimpio.length);
        });

        elemento.addEventListener('input', function() {
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    }

    // Aplicar blindaje a los inputs de nombre de crear y editar
    blindarSoloLetras(document.getElementById('nombre_crear'));
    blindarSoloLetras(document.getElementById('edit_nombre'));

    // Rellenar dinámicamente el modal de edición
    const botonesEditar = document.querySelectorAll('.btn-editar');
    botonesEditar.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const email = this.getAttribute('data-email');
            const telefono = this.getAttribute('data-telefono');
            const activo = this.getAttribute('data-activo');

            const form = document.getElementById('formEditarCliente');
            form.action = `/admin/usuarios/${id}`;

            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_telefono').value = telefono !== 'null' ? telefono : '';
            document.getElementById('edit_activo').value = activo;

            new bootstrap.Modal(document.getElementById('modalEditarCliente')).show();
        });
    });
});
</script>

<style>
    .card-modern { box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.04) !important; border: none; }
    .transition-row { transition: background-color 0.2s ease; }
    .transition-row:hover { background-color: #fafbfd !important; }
    .btn-video-glow { transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(13, 148, 136, 0.3); }
    .btn-video-glow:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 8px 25px rgba(13, 148, 136, 0.5); }
    .btn-action { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid transparent; transition: all 0.2s ease; background: #f1f3f5; cursor: pointer; }
    .btn-action-info { color: #0dcaf0; background: #eefcfd; }
    .btn-action-info:hover { background-color: #0dcaf0; color: #000; transform: translateY(-2px); }
    .btn-action-danger { color: #dc3545; background: #fef2f2; }
    .btn-action-danger:hover { background-color: #dc3545; color: #fff; transform: translateY(-2px); }
    .btn-action-success { color: #198754; background: #edf7f2; }
    .btn-action-success:hover { background-color: #198754; color: #fff; transform: translateY(-2px); }
    .btn-no-select { user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; }
    .form-control:focus, .form-select:focus { border-color: #0d9488 !important; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1) !important; }
    /* Spinner de Tijeras Animadas */
    .scissors-loader {
        display: inline-block;
        animation: scissorsSpin 0.9s linear infinite;
    }
    @keyframes scissorsSpin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection