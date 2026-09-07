@extends('layouts.barbero')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center text-success">Agenda del Barbero</h1>

    <!-- Filtro en vivo -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-center align-items-center gap-2">
            <!-- Calendario -->
            <input type="date" id="filtroFecha" class="form-control w-auto">

            <!-- Combobox día/mes/año -->
            <select id="filtroDia" class="form-select w-auto">
                <option value="">Día</option>
                @for($i=1;$i<=31;$i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            <select id="filtroMes" class="form-select w-auto">
                <option value="">Mes</option>
                @for($i=1;$i<=12;$i++)
                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->locale('es')->translatedFormat('F') }}</option>
                @endfor
            </select>

            <select id="filtroAnio" class="form-select w-auto">
                <option value="">Año</option>
                @for($i=date('Y');$i<=date('Y')+1;$i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>

            <!-- Rangos rápidos -->
            <select id="filtroRango" class="form-select w-auto">
                <option value="">Rango</option>
                <option value="3">Últimos 3 días</option>
                <option value="7">Últimos 7 días</option>
                <option value="30">Últimos 30 días</option>
            </select>
        </div>
    </div>

    @php
        use Carbon\Carbon;
        $hoy = Carbon::today()->toDateString();

        $turnos = \App\Models\Turno::with(['cliente','servicio','sucursal'])
                    ->where('barbero_id', Auth::id())
                    ->orderBy('fecha')
                    ->orderBy('hora')
                    ->get();

        $turnosHoy = $turnos->where('fecha', $hoy);
        $turnosFuturos = $turnos->where('fecha', '>', $hoy)->groupBy('fecha');
        $turnosPasados = $turnos->where('fecha', '<', $hoy)->groupBy('fecha');
    @endphp

    <!-- Turnos de hoy -->
    <div class="card border-primary mb-4">
        <div class="card-header bg-primary text-white">
            Turnos de hoy ({{ Carbon::parse($hoy)->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }})
        </div>
        <div class="card-body">
            @if($turnosHoy->isEmpty())
                <p class="text-center">No tenés turnos para hoy.</p>
            @else
                @include('barbero.partials.tabla_turnos',['lista'=>$turnosHoy])
            @endif
        </div>
    </div>

    <!-- Turnos futuros -->
    <div class="card border-success mb-4">
        <div class="card-header bg-success text-white">Turnos futuros</div>
        <div class="card-body">
            @foreach($turnosFuturos as $fecha => $lista)
                <h5 class="text-success">{{ Carbon::parse($fecha)->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }}</h5>
                @include('barbero.partials.tabla_turnos',['lista'=>$lista])
            @endforeach
        </div>
    </div>

    <!-- Turnos pasados -->
    <div class="card border-secondary mb-4">
        <div class="card-header bg-secondary text-white">Turnos pasados</div>
        <div class="card-body">
            @foreach($turnosPasados->sortKeysDesc() as $fecha => $lista)
                <h5 class="text-muted">{{ Carbon::parse($fecha)->locale('es')->translatedFormat('l d \\d\\e F \\d\\e Y') }}</h5>
                @include('barbero.partials.tabla_turnos',['lista'=>$lista])
            @endforeach
        </div>
    </div>
</div>

<!-- Script para filtro en vivo -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filtroFecha = document.getElementById('filtroFecha');
    const filtroDia = document.getElementById('filtroDia');
    const filtroMes = document.getElementById('filtroMes');
    const filtroAnio = document.getElementById('filtroAnio');
    const filtroRango = document.getElementById('filtroRango');

    function aplicarFiltro() {
        // Aquí podés hacer fetch/ajax para recargar las tablas en vivo
        console.log("Filtro aplicado:", {
            fecha: filtroFecha.value,
            dia: filtroDia.value,
            mes: filtroMes.value,
            anio: filtroAnio.value,
            rango: filtroRango.value
        });
    }

    [filtroFecha, filtroDia, filtroMes, filtroAnio, filtroRango].forEach(el => {
        el.addEventListener('change', aplicarFiltro);
    });
});
</script>
@endsection

