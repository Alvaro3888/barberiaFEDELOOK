@extends('layouts.public')

@section('content')
<div class="container mt-5 pt-4 mb-5" style="max-width: 700px;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
        
        <div class="card-header bg-dark text-white p-4 text-center">
            <h3 class="fw-bold mb-1">Solicitar Nuevo Turno</h3>
            <p class="text-white-50 small mb-0">Seleccioná tus servicios y completá los datos para tu reserva</p>
        </div>

        <div class="card-body p-4 p-md-5">

            <form action="{{ route('usuario.turnos.store') }}" method="POST" id="formTurno">
                @csrf

                <!-- Sucursal -->
                <div class="mb-4">
                    <label for="sucursal_id" class="form-label fw-semibold text-secondary">Sucursal</label>
                    <select name="sucursal_id" id="sucursal_id" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required>
                        <option value="">Seleccioná una sucursal</option>
                        @if(isset($sucursales))
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Servicios Seleccionados -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary d-flex justify-content-between align-items-center">
                        <span>Servicios a contratar <span class="text-danger">*</span></span>
                        <a href="#" id="btnIrAlCatalogo" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold text-decoration-none fs-7">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Servicio
                        </a>
                    </label>

                    <div id="serviciosSeleccionadosContainer" class="d-flex flex-wrap gap-2 mb-2 p-3 bg-light rounded-3 border">
                        <span class="text-muted small fst-italic" id="sinServiciosMsg">Ningún servicio seleccionado todavía. Hacé clic en "Agregar Servicio".</span>
                    </div>

                    <!-- Inputs ocultos que viajan al controlador Laravel como array -->
                    <div id="inputsServiciosOcultos"></div>

                    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
                        <small class="text-muted fw-medium">Duración total estimada: <span id="minutosTotales">0</span> min</small>
                        <small class="fw-bold text-dark fs-6">Total a abonar: $<span id="montoTotalMostrador">0</span></small>
                    </div>
                </div>

                <!-- Barbero -->
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Barbero <span class="text-danger">*</span></label>
                    
                    <input type="hidden" name="barbero_id" id="barbero_id" value="{{ old('barbero_id') }}" required>
                    
                    <div id="barberosContainer" class="barberos-lista-horizontal d-flex align-items-center gap-4 overflow-auto py-2 px-1 text-muted small fst-italic rounded-3 bg-light border p-2" style="scrollbar-width: none; -ms-overflow-style: none; min-height: 90px;">
                        Seleccioná una sucursal para ver los profesionales disponibles.
                    </div>

                    <div id="barberoAgendaInfo" class="alert py-2 px-3 mt-2 mb-0 d-none small fw-medium"></div>
                </div>

                <div class="row">
                    <!-- Fecha -->
                    <div class="col-md-6 mb-3">
                        <label for="fecha" class="form-label fw-semibold text-secondary">Fecha</label>
                        <input type="date" name="fecha" id="fecha" class="form-control form-control-lg fs-6 rounded-3 shadow-sm" required disabled>
                    </div>

                    <!-- Hora -->
                    <div class="col-md-6 mb-3">
                        <label for="hora" class="form-label fw-semibold text-secondary">Hora</label>
                        <select name="hora" id="hora" class="form-select form-select-lg fs-6 rounded-3 shadow-sm" required disabled>
                            <option value="">Seleccioná un barbero y fecha primero</option>
                        </select>
                        <small class="text-muted d-block mt-1">Intervalos de 15 minutos</small>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" id="btnConfirmar" class="btn btn-dark btn-lg py-3 rounded-3 fw-bold shadow-sm" disabled>
                        Confirmar Turno y Pagar Online
                    </button>
                    <a href="{{ route('usuario.turnos') }}" class="btn btn-outline-secondary btn-lg py-2 rounded-3 text-decoration-none text-center">
                        Volver
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>

<style>
    .barberos-lista-horizontal::-webkit-scrollbar { display: none; }
    .barbero-item-h { cursor: pointer; }
    .barbero-item-h:hover .barbero-img { border-color: #0d9488 !important; transform: scale(1.05); }
    .barbero-item-h.seleccionado .barbero-img { border-color: #0d9488 !important; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2) !important; }
    .barbero-item-h.seleccionado .check-overlay { opacity: 1 !important; }
    .barbero-item-h.seleccionado .barbero-nombre-label { color: #0d9488 !important; font-weight: 700 !important; }
    .text-teal { color: #0d9488 !important; }
    .bg-teal-soft { background-color: #f0fdfa !important; }
    .bg-teal { background-color: #0d9488 !important; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const sucursalSel       = document.getElementById('sucursal_id');
    const fechaInput        = document.getElementById('fecha');
    const horaInput         = document.getElementById('hora');
    const barberosContainer = document.getElementById('barberosContainer');
    const inputBarberoId    = document.getElementById('barbero_id');
    const barberoAgenda     = document.getElementById('barberoAgendaInfo');
    const btnConfirmar      = document.getElementById('btnConfirmar');
    const btnIrAlCatalogo   = document.getElementById('btnIrAlCatalogo');
    const formTurno         = document.getElementById('formTurno');

    let cacheBarberosSucursal = [];
    let serviciosElegidos = JSON.parse(localStorage.getItem('form_servicios') || '[]');

    let hoyStr = new Date().toISOString().split('T')[0];
    if (fechaInput) {
        fechaInput.min = hoyStr;
    }

    function restaurarEstadoFormulario() {
        const savedSucursal = localStorage.getItem('form_sucursal_id');
        const savedFecha = localStorage.getItem('form_fecha');
        const savedBarberoId = localStorage.getItem('form_barbero_id');
        const savedHora = localStorage.getItem('form_hora');

        if (savedSucursal && sucursalSel) {
            sucursalSel.value = savedSucursal;
            
            fetch('/barberos/' + savedSucursal)
                .then(response => response.json())
                .then(data => {
                    cacheBarberosSucursal = data || [];
                    
                    if (cacheBarberosSucursal.length > 0) {
                        renderizarBarberos(savedBarberoId);
                    }

                    if (savedBarberoId && savedFecha) {
                        fechaInput.disabled = false;
                        fechaInput.value = savedFecha;
                        inputBarberoId.value = savedBarberoId;

                        if (savedHora) {
                            cargarHorariosDisponibles(savedHora);
                        }
                    }
                })
                .catch(() => { cacheBarberosSucursal = []; });
        }
    }

    @if(isset($servicioSeleccionado) && $servicioSeleccionado)
        let nuevoServicio = @json($servicioSeleccionado);
        let existeId = serviciosElegidos.some(s => s.id === nuevoServicio.id);
        let existeTipo = serviciosElegidos.some(s => s.tipo_servicio_id && s.tipo_servicio_id === nuevoServicio.tipo_servicio_id);
        
        if (!existeId && !existeTipo) {
            serviciosElegidos.push(nuevoServicio);
            localStorage.setItem('form_servicios', JSON.stringify(serviciosElegidos));
        }
    @endif

    renderizarServiciosElegidos();
    restaurarEstadoFormulario();

    if (sucursalSel) {
        sucursalSel.addEventListener('change', function() {
            let sucursalId = this.value;
            localStorage.setItem('form_sucursal_id', sucursalId);
            localStorage.removeItem('form_fecha');
            localStorage.removeItem('form_barbero_id');
            localStorage.removeItem('form_hora');
            
            fechaInput.value = '';
            fechaInput.disabled = true;
            horaInput.innerHTML = '<option value="">Seleccioná un barbero y fecha primero</option>';
            horaInput.disabled = true;
            barberosContainer.innerHTML = '<span class="text-muted fst-italic">Seleccioná una sucursal válida.</span>';
            inputBarberoId.value = '';
            if (barberoAgenda) barberoAgenda.classList.add('d-none');
            if (btnConfirmar) btnConfirmar.disabled = true;
            cacheBarberosSucursal = [];

            if (sucursalId) {
                fetch('/barberos/' + sucursalId)
                    .then(response => response.json())
                    .then(data => { 
                        cacheBarberosSucursal = data || []; 
                        renderizarBarberos(null);
                    })
                    .catch(() => { cacheBarberosSucursal = []; });
            }
        });
    }

    function renderizarBarberos(barberoARestaurar) {
        if (cacheBarberosSucursal.length === 0) {
            barberosContainer.innerHTML = '<span class="text-danger fst-italic">No hay profesionales disponibles en esta sucursal.</span>';
            return;
        }

        let html = '';
        cacheBarberosSucursal.forEach(barbero => {
            let iniciales = barbero.nombre.substring(0, 2).toUpperCase();
            let primerNombre = barbero.nombre.trim().split(' ')[0];
            let fotoPerfil = barbero.avatar || barbero.imagen;
            let imgHtml = '';

            if (fotoPerfil) {
                imgHtml = `<img src="/storage/${fotoPerfil}" alt="${barbero.nombre}" class="rounded-circle object-fit-cover barbero-img shadow-xs" style="width: 60px; height: 60px; border: 2px solid #e2e8f0; transition: all 0.2s ease;">`;
            } else if (barbero.imagen_url) {
                imgHtml = `<img src="${barbero.imagen_url}" alt="${barbero.nombre}" class="rounded-circle object-fit-cover barbero-img shadow-xs" style="width: 60px; height: 60px; border: 2px solid #e2e8f0; transition: all 0.2s ease;">`;
            } else {
                imgHtml = `<div class="rounded-circle bg-teal-soft text-teal d-flex align-items-center justify-content-center fw-bold barbero-img shadow-xs mx-auto" style="width: 60px; height: 60px; font-size: 1rem; border: 2px solid #ccfbf1; transition: all 0.2s ease;">${iniciales}</div>`;
            }

            let claseSeleccionado = (barberoARestaurar && barbero.id == barberoARestaurar) ? 'seleccionado' : '';

            html += `
                <div class="barbero-item-h text-center ${claseSeleccionado}" 
                     data-id="${barbero.id}" 
                     style="cursor: pointer; flex: 0 0 auto; width: 75px; transition: transform 0.2s ease;">
                    <div class="avatar-wrapper position-relative d-inline-block mb-1">
                        ${imgHtml}
                        <div class="check-overlay position-absolute top-0 start-0 w-100 h-100 rounded-circle bg-teal bg-opacity-75 d-flex align-items-center justify-content-center text-white opacity-0 transition-opacity" style="transition: opacity 0.2s ease;">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                    </div>
                    <span class="d-block text-dark fw-semibold text-truncate barbero-nombre-label" style="font-size: 0.72rem; line-height: 1.2;" title="${barbero.nombre}">
                        ${primerNombre}
                    </span>
                </div>
            `;
        });
        barberosContainer.innerHTML = html;
        activarClicksAvatares();
    }

    function activarClicksAvatares() {
        const itemsBarberos = document.querySelectorAll('.barbero-item-h');
        itemsBarberos.forEach(item => {
            item.addEventListener('click', function() {
                itemsBarberos.forEach(i => i.classList.remove('seleccionado'));
                this.classList.add('seleccionado');
                let bId = this.getAttribute('data-id');
                inputBarberoId.value = bId;
                localStorage.setItem('form_barbero_id', bId);
                
                fechaInput.disabled = false;
                if (fechaInput.value) {
                    cargarHorariosDisponibles(null);
                }
            });
        });
    }

    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            localStorage.setItem('form_fecha', this.value);
            localStorage.removeItem('form_hora');
            horaInput.innerHTML = '<option value="">Seleccioná un horario</option>';
            horaInput.disabled = true;
            if (btnConfirmar) btnConfirmar.disabled = true;

            if (this.value && inputBarberoId.value) {
                cargarHorariosDisponibles(null);
            }
        });
    }

    function cargarHorariosDisponibles(horaARestaurar) {
        let barberoId = inputBarberoId.value;
        let sucursalId = sucursalSel.value;
        let fechaVal = fechaInput.value;
        
        let duracionTotal = serviciosElegidos.reduce((acc, s) => acc + parseInt(s.duracion || 30), 0);

        horaInput.innerHTML = '<option value="">Calculando horarios disponibles...</option>';
        horaInput.disabled = true;
        btnConfirmar.disabled = true;
        if (barberoAgenda) barberoAgenda.classList.add('d-none');

        if (!barberoId || !sucursalId || !fechaVal || serviciosElegidos.length === 0) {
            horaInput.innerHTML = '<option value="">Faltan datos (servicios, fecha o barbero)</option>';
            return;
        }

        fetch(`/turnos/horarios-disponibles?barbero_id=${barberoId}&sucursal_id=${sucursalId}&fecha=${fechaVal}&duracion_total=${duracionTotal}`)
            .then(response => response.json())
            .then(slots => {
                horaInput.innerHTML = '<option value="">Seleccioná un horario</option>';
                if (slots.length > 0) {
                    slots.forEach(slot => {
                        let opt = document.createElement('option');
                        opt.value = slot.hora;
                        opt.textContent = slot.formato;
                        if (horaARestaurar && slot.hora === horaARestaurar) {
                            opt.selected = true;
                        }
                        horaInput.appendChild(opt);
                    });
                    horaInput.disabled = false;

                    if (horaARestaurar) {
                        localStorage.setItem('form_hora', horaARestaurar);
                        validarBotonConfirmar();
                    }

                    barberoAgenda.className = "alert alert-success py-2 px-3 mt-2 mb-0 small fw-medium border-success text-dark";
                    barberoAgenda.innerHTML = `✅ Agenda disponible. Duración total del servicio: <strong>${duracionTotal} min</strong>.`;
                    barberoAgenda.classList.remove('d-none');
                } else {
                    horaInput.innerHTML = '<option value="">No hay horarios libres para esta duración</option>';
                    horaInput.disabled = true;
                    barberoAgenda.className = "alert alert-danger py-2 px-3 mt-2 mb-0 small fw-medium border-danger text-dark";
                    barberoAgenda.innerHTML = `⚠️ El profesional no tiene espacio libre suficiente para los ${duracionTotal} min requeridos en esta fecha.`;
                    barberoAgenda.classList.remove('d-none');
                }
            })
            .catch(err => {
                console.error('Error al consultar disponibilidad:', err);
                horaInput.innerHTML = '<option value="">Error al cargar horarios</option>';
            });
    }

    if (horaInput) {
        horaInput.addEventListener('change', function() {
            localStorage.setItem('form_hora', this.value);
            validarBotonConfirmar();
        });
    }

    if (btnIrAlCatalogo) {
        btnIrAlCatalogo.addEventListener('click', function(e) {
            e.preventDefault();
            let sucursalId = sucursalSel ? sucursalSel.value : '';
            
            if (!sucursalId) {
                alert('Por favor, seleccioná una sucursal primero.');
                sucursalSel.focus();
                return;
            }

            localStorage.setItem('form_servicios', JSON.stringify(serviciosElegidos));
            localStorage.setItem('form_sucursal_id', sucursalId);
            if (fechaInput.value) localStorage.setItem('form_fecha', fechaInput.value);
            if (inputBarberoId.value) localStorage.setItem('form_barbero_id', inputBarberoId.value);
            if (horaInput.value) localStorage.setItem('form_hora', horaInput.value);

            let tiposExcluidos = serviciosElegidos.map(s => s.tipo_servicio_id).filter(Boolean);
            let url = "{{ route('usuario.catalogo') }}?sucursal_id=" + sucursalId;
            
            tiposExcluidos.forEach(tipoId => {
                url += `&excluir_tipos[]=${tipoId}`;
            });

            window.location.href = url;
        });
    }

    function renderizarServiciosElegidos() {
        const container = document.getElementById('serviciosSeleccionadosContainer');
        const inputsContainer = document.getElementById('inputsServiciosOcultos');
        const msgVacio = document.getElementById('sinServiciosMsg');
        const spanMinutos = document.getElementById('minutosTotales');
        const spanMonto = document.getElementById('montoTotalMostrador');

        container.innerHTML = '';
        inputsContainer.innerHTML = '';

        if (serviciosElegidos.length === 0) {
            container.appendChild(msgVacio);
            spanMinutos.textContent = 0;
            spanMonto.textContent = 0;
            validarBotonConfirmar();
            return;
        }

        let duracionTotal = 0;
        let precioTotal = 0;

        serviciosElegidos.forEach(s => {
            duracionTotal += parseInt(s.duracion || 30);
            precioTotal += parseFloat(s.precio_actual ?? s.precio ?? 0);

            const badge = document.createElement('span');
            badge.className = 'badge bg-teal text-white p-2 d-inline-flex align-items-center gap-2 fs-7';
            badge.innerHTML = `${s.nombre} ($${s.precio_actual ?? s.precio} - ${s.duracion} min) <button type="button" class="btn-close btn-close-white btn-xs" style="font-size: 0.65rem;" onclick="quitarServicio(${s.id})"></button>`;
            container.appendChild(badge);

            const inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = 'servicios[]';
            inputHidden.value = s.id;
            inputsContainer.appendChild(inputHidden);
        });

        spanMinutos.textContent = duracionTotal;
        spanMonto.textContent = precioTotal.toFixed(2);

        localStorage.setItem('form_servicios', JSON.stringify(serviciosElegidos));
        
        if (inputBarberoId.value && fechaInput.value) {
            cargarHorariosDisponibles(horaInput.value);
        }

        validarBotonConfirmar();
    }

    window.quitarServicio = function(id) {
        serviciosElegidos = serviciosElegidos.filter(s => s.id !== id);
        renderizarServiciosElegidos();
    }

    if (formTurno) {
        formTurno.addEventListener('submit', function() {
            localStorage.removeItem('form_servicios');
            localStorage.removeItem('form_sucursal_id');
            localStorage.removeItem('form_fecha');
            localStorage.removeItem('form_hora');
            localStorage.removeItem('form_barbero_id');
        });
    }

    function validarBotonConfirmar() {
        if (inputBarberoId.value && fechaInput.value && horaInput.value && serviciosElegidos.length > 0) {
            btnConfirmar.disabled = false;
        } else {
            btnConfirmar.disabled = true;
        }
    }
});
</script>
@endsection