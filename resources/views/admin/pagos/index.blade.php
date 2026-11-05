@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">Movimientos de Pagos</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de Filtros en Vivo (JavaScript) -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filter-cliente" class="form-label font-weight-bold">Cliente</label>
                    <input type="text" id="filter-cliente" class="form-control" placeholder="Buscar por cliente...">
                </div>

                <div class="col-md-3">
                    <label for="filter-servicio" class="form-label font-weight-bold">Servicio</label>
                    <select id="filter-servicio" class="form-select">
                        <option value="">Todos los servicios</option>
                        @foreach($servicios as $servicio)
                            <option value="{{ strtolower($servicio->nombre) }}">
                                {{ $servicio->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="filter-payment" class="form-label font-weight-bold">Payment ID (Mercado Pago)</label>
                    <input type="text" id="filter-payment" class="form-control" placeholder="Buscar Payment ID...">
                </div>

                <div class="col-md-3">
                    <button type="button" id="btn-limpiar" class="btn btn-secondary w-100">
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Pagos -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="tabla-pagos">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Barbero</th>
                            <th>Sucursal</th>
                            <th>Servicio</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Payment ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pagos as $pago)
                            <tr class="fila-pago" 
                                data-cliente="{{ strtolower($pago->turno?->cliente?->nombre ?? '') }}"
                                data-servicio="{{ strtolower($pago->turno?->servicio?->nombre ?? '') }}"
                                data-payment="{{ strtolower($pago->payment_id_mp ?? '') }}">
                                
                                <td>
                                    {{ $pago->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td>
                                    {{ $pago->turno?->cliente?->nombre ?? 'N/D' }}
                                </td>

                                <td>
                                    {{ $pago->turno?->barbero?->nombre ?? 'N/D' }}
                                </td>

                                <td>
                                    {{ $pago->turno?->sucursal?->nombre ?? 'N/D' }}
                                </td>

                                <td>
                                    {{ $pago->turno?->servicio?->nombre ?? 'N/D' }}
                                </td>

                                <td>
                                    ${{ number_format($pago->monto, 0, ',', '.') }}
                                </td>

                                <td>
                                    {{ ucfirst($pago->metodo) }}
                                </td>

                                <td>
                                    @if($pago->estado === 'pagado')
                                        <span class="badge bg-success">
                                            Pagado
                                        </span>
                                    @elseif($pago->estado === 'pendiente')
                                        <span class="badge bg-warning text-dark">
                                            Pendiente
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            {{ ucfirst($pago->estado) }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $pago->payment_id_mp ?? 'N/D' }}
                                </td>
                            </tr>
                        @empty
                            <tr id="sin-registros-inicial">
                                <td colspan="9" class="text-center py-3">
                                    No existen pagos registrados.
                                </td>
                            </tr>
                        @endforelse

                        <tr id="sin-coincidencias" class="d-none">
                            <td colspan="9" class="text-center py-3">
                                No se encontraron pagos que coincidan con la búsqueda.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Script de filtrado en vivo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputCliente = document.getElementById('filter-cliente');
    const selectServicio = document.getElementById('filter-servicio');
    const inputPayment = document.getElementById('filter-payment');
    const btnLimpiar = document.getElementById('btn-limpiar');
    const filas = document.querySelectorAll('.fila-pago');
    const filaSinCoincidencias = document.getElementById('sin-coincidencias');

    function filtrarTabla() {
        const valCliente = inputCliente.value.toLowerCase().trim();
        const valServicio = selectServicio.value.toLowerCase().trim();
        const valPayment = inputPayment.value.toLowerCase().trim();

        let coincidencias = 0;

        filas.forEach(function(fila) {
            const cliente = fila.getAttribute('data-cliente');
            const servicio = fila.getAttribute('data-servicio');
            const payment = fila.getAttribute('data-payment');

            const coincideCliente = cliente.includes(valCliente);
            const coincideServicio = valServicio === '' || servicio === valServicio;
            const coincidePayment = payment.includes(valPayment);

            if (coincideCliente && coincideServicio && coincidePayment) {
                fila.classList.remove('d-none');
                coincidencias++;
            } else {
                fila.classList.add('d-none');
            }
        });

        if (filas.length > 0) {
            if (coincidencias === 0) {
                filaSinCoincidencias.classList.remove('d-none');
            } else {
                filaSinCoincidencias.classList.add('d-none');
            }
        }
    }

    inputCliente.addEventListener('input', filtrarTabla);
    selectServicio.addEventListener('change', filtrarTabla);
    inputPayment.addEventListener('input', filtrarTabla);

    btnLimpiar.addEventListener('click', function() {
        inputCliente.value = '';
        selectServicio.value = '';
        inputPayment.value = '';
        filtrarTabla();
    });
});
</script>

@endsection