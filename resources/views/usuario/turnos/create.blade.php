@extends('layouts.public')

@section('content')
<div class="container my-5 bg-paper border-ink p-4 rounded">

    <h2 class="section-title">Solicitar nuevo turno</h2>

    {{-- Mensajes de error backend --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-charcoal">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('usuario.turnos.store') }}" method="POST">
        @csrf

        <!-- Sucursal -->
        <div class="mb-3">
            <label for="sucursal_id" class="form-label text-ink">Sucursal</label>
            <select name="sucursal_id" id="sucursal_id" class="form-select" required>
                <option value="">Seleccioná una sucursal</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Servicio -->
        <div class="mb-3">
            <label for="servicio_id" class="form-label text-ink">Servicio</label>
            <select name="servicio_id" id="servicio_id" class="form-select" required disabled>
                <option value="">Seleccioná primero una sucursal</option>
                @foreach($servicios as $servicio)
                    <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Barbero -->
        <div class="mb-3">
            <label for="barbero_id" class="form-label text-ink">Barbero</label>
            <select name="barbero_id" id="barbero_id" class="form-select" required disabled>
                <option value="">Seleccioná primero una sucursal</option>
            </select>
        </div>

        <!-- Fecha -->
        <div class="mb-3">
            <label for="fecha" class="form-label text-ink">Fecha</label>
            <input type="date" name="fecha" id="fecha" class="form-control" required>
            <small id="fechaError" class="text-danger d-none"></small>
        </div>

        <!-- Hora -->
        <div class="mb-3">
            <label for="hora" class="form-label text-ink">Hora</label>
            <input type="time" name="hora" id="hora" class="form-control" required>
            <small id="horaError" class="text-danger d-none"></small>
        </div>

        <!-- Botones -->
        <button type="submit" id="btnConfirmar" class="btn btn-oxblood fw-bold" disabled>Confirmar turno</button>
        <a href="{{ route('usuario.turnos') }}" class="btn btn-outline-ink">Volver</a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fechaInput   = document.getElementById('fecha');
    const horaInput    = document.getElementById('hora');
    const fechaError   = document.getElementById('fechaError');
    const horaError    = document.getElementById('horaError');
    const sucursalSel  = document.getElementById('sucursal_id');
    const servicioSel  = document.getElementById('servicio_id');
    const barberoSel   = document.getElementById('barbero_id');
    const btnConfirmar = document.getElementById('btnConfirmar');

    // --- 1. Bloquear fechas anteriores ---
    fechaInput.min = new Date().toISOString().split("T")[0];

    function validarHora() {
        fechaError.classList.add("d-none");
        horaError.classList.add("d-none");

        if (!fechaInput.value || !horaInput.value) return;

        let seleccionada = new Date(fechaInput.value + "T" + horaInput.value);
        let ahora = new Date();

        // Si la fecha/hora es anterior
        if (seleccionada < ahora) {
            horaError.textContent = "La hora seleccionada ya pasó.";
            horaError.classList.remove("d-none");
            horaInput.value = "";
            btnConfirmar.disabled = true;
            return;
        }

        // Si es menos de 30 minutos de anticipación
        let diferencia = (seleccionada - ahora) / 60000; // minutos
        if (diferencia < 30) {
            horaError.textContent = "El turno debe reservarse con al menos 30 minutos de anticipación.";
            horaError.classList.remove("d-none");
            horaInput.value = "";
            btnConfirmar.disabled = true;
            return;
        }

        btnConfirmar.disabled = false;
    }

    fechaInput.addEventListener("change", validarHora);
    horaInput.addEventListener("change", validarHora);

    // --- 2. Flujo de combos ---
    servicioSel.disabled = true;
    barberoSel.disabled  = true;

    sucursalSel.addEventListener('change', function() {
        let sucursalId = this.value;

        servicioSel.disabled = !sucursalId;
        barberoSel.disabled  = !sucursalId;

        if (sucursalId) {
            barberoSel.innerHTML = '<option value="">Cargando...</option>';
            fetch('/barberos/' + sucursalId)
                .then(response => response.json())
                .then(data => {
                    barberoSel.innerHTML = '<option value="">Seleccioná un barbero</option>';
                    data.forEach(barbero => {
                        barberoSel.innerHTML += `<option value="${barbero.id}">${barbero.nombre}</option>`;
                    });
                });
        } else {
            barberoSel.innerHTML = '<option value="">Seleccioná un barbero</option>';
        }
    });
});
</script>

@endsection
