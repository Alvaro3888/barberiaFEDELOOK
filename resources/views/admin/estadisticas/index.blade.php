@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 text-primary">Estadísticas</h1>
    @include('admin.estadisticas.partials.estadisticas_table', [
        'ingresosMes' => $ingresosMes,
        'totalTurnosMes' => $totalTurnosMes,
        'servicioMasSolicitado' => $servicioMasSolicitado,
        'barberoDestacado' => $barberoDestacado
    ])
</div>
@endsection
