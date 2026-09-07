@extends('layouts.public')

@section('content')
<div class="container my-5 bg-paper border-ink p-4 rounded">

    <h2 class="section-title">Mis turnos</h2>

    @if(session('success'))
        <div class="alert alert-success text-ink">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-ink">{{ session('error') }}</div>
    @endif

    <a href="{{ route('usuario.turnos.create') }}" class="btn btn-oxblood fw-bold mb-3">
        Solicitar nuevo turno →
    </a>

    <table class="table table-bordered border-ink">
        <thead class="bg-paper-light">
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <th>Sucursal</th>
                <th>Barbero</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($turnos as $turno)
                @php
                    $horaTurno = \Carbon\Carbon::parse($turno->fecha.' '.$turno->hora);
                    $minutosRestantes = now()->diffInMinutes($horaTurno, false);
                @endphp
                <tr>
                    <td>{{ $turno->fecha }}</td>
                    <td>{{ $turno->hora }}</td>
                    <td>{{ $turno->servicio->nombre }}</td>
                    <td>{{ $turno->sucursal->nombre }}</td>
                    <td>{{ $turno->barbero->nombre }}</td>
                    <td>
                        @if($turno->estado === 'confirmado')
                            <span class="badge bg-info text-dark">Confirmado</span>
                        @elseif($turno->estado === 'completado')
                            <span class="badge bg-success">Completado</span>
                        @elseif($turno->estado === 'cancelado')
                            <span class="badge bg-secondary">Cancelado</span>
                        @endif
                    </td>
                    <td>
                        @if($turno->estado === 'confirmado' && $minutosRestantes >= 30)
                            <a href="{{ route('usuario.turnos.edit', $turno->id) }}" 
                               class="btn btn-outline-ink btn-sm">Editar</a>

                            <form action="{{ route('usuario.turnos.destroy', $turno->id) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-oxblood btn-sm"
                                        onclick="return confirm('¿Seguro que querés cancelar este turno?')">
                                    Cancelar
                                </button>
                            </form>
                        @else
                            <span class="text-muted">Sin acciones</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No tenés turnos reservados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
