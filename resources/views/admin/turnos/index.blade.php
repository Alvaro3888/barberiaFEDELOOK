@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h2 class="text-primary">Gestión de Turnos</h2>
    </div>

    <!-- Filtros dinámicos -->
    <div class="row mb-3">
        <div class="col-md-3">
            <select id="sucursalFilter" class="form-select">
                <option value="">Todas las sucursales</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select id="rangeFilter" class="form-select">
                <option value="">Todos</option>
                <option value="hoy">Hoy</option>
                <option value="3">Últimos 3 días</option>
                <option value="7">Últimos 7 días</option>
                <option value="30">Últimos 30 días</option>
            </select>
        </div>
    </div>

    <!-- Tabla dinámica -->
    <div id="turnosTable">
        @include('admin.turnos.partials.turnos_table', ['turnos' => $turnos])
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sucursalFilter = document.getElementById('sucursalFilter');
    const rangeFilter = document.getElementById('rangeFilter');
    const turnosTable = document.getElementById('turnosTable');

    function loadTurnos() {
        const sucursal = sucursalFilter.value;
        const range = rangeFilter.value;

        fetch(`{{ route('admin.turnos.index') }}?sucursal_id=${sucursal}&range=${range}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            turnosTable.innerHTML = html;
        });
    }

    sucursalFilter.addEventListener('change', loadTurnos);
    rangeFilter.addEventListener('change', loadTurnos);
});
</script>
@endsection
