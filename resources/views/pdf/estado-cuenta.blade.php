@extends('pdf.membrete.membrete')

@section('content')

<style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th,
    td {
        border: 1px solid #444;
        padding: 5px;
        text-align: left;
    }

    th {
        background: #f2f2f2;
        font-weight: bold;
    }

    h2 {
        text-align: center;
        margin-bottom: 15px;
    }
</style>
<h2>ESTADO DE CUENTA</h2>

<table>
    <tr>
        <th style="width:25%;">Proyecto</th>
        <td colspan="3">{{ $estado_cuenta['Proyecto'] ?? '-' }}</td>
    </tr>

    <tr>
        <th>Etapa</th>
        <td>{{ $estado_cuenta['Etapa'] ?? '-' }}</td>

        <th>Manzana - Lote</th>
        <td>
            {{ $estado_cuenta['Manzana'] ?? '-' }}
            -
            {{ $estado_cuenta['Lote'] ?? '-' }}
        </td>
    </tr>

    <tr>
        <th>Nombre Cliente</th>
        <td colspan="3">
            {{ $estado_cuenta['Cliente'] ?? '-' }}
        </td>
    </tr>

    <tr>
        <th>DNI</th>
        <td>{{ $estado_cuenta['DNI'] ?? '-' }}</td>

        <th>Fecha emisión</th>
        <td>{{ $estado_cuenta['FecEmision'] ?? '-' }}</td>
    </tr>

    <tr>
        <th>Precio venta</th>
        <td>S/ {{ $estado_cuenta['Venta'] ?? '-' }}</td>

        <th>Impor. Financiado</th>
        <td>S/ {{ $estado_cuenta['ImporteFinanciado'] ?? '-' }}</td>
    </tr>

    <tr>
        <th>Inicial</th>
        <td>S/ {{ $estado_cuenta['Inicial'] ?? '-' }}</td>

        <th>Exonerada</th>
        <td>S/ {{ $estado_cuenta['Exonerado'] ?? '-' }}</td>
    </tr>

    <tr>
        <th>Capital abonado</th>
        <td>S/ {{ $estado_cuenta['CapitalAbonado'] ?? '-' }}</td>

        <th>Penalidad abonado</th>
        <td></td>
    </tr>

    <tr>
        <th>Saldo total pend.</th>
        <td>S/ {{ $estado_cuenta['SaldoTotalPendiente'] ?? '-' }}</td>

        <th>Saldo capital pend.</th>
        <td>S/ {{ $estado_cuenta['SaldoCapitalPendiente'] ?? '-' }}</td>
    </tr>

    <tr>
        <th>Ult. Edición</th>
        <td>{{ $estado_cuenta['UltimaEdicion'] ?? '-' }}</td>

        <th>N° Cuotas pend..</th>
        <td>{{ $estado_cuenta['NroCuotasPendiente'] ?? '-' }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>N°</th>
            <th>Fecha Compra</th>
            <th>Fecha Venc.</th>
            <th>Cuota</th>
            <th>Días de Atrazo</th>
            <th>Penalidad</th>
            <th>Total</th>
            <th>Cuota Pagada</th>
            <th>Penalidad Pagada</th>
            <th>Monto Total Pagado</th>
            {{-- <th>Penalidad</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach (($estado_cuenta['Cuotas'] ?? []) as $item)
        <tr>
            <td>{{ $item['NroCuota'] ?? '-' }}</td>
            <td>{{ $item['FecCompra'] ?? '-' }}</td>
            <td>{{ $item['FecVencimiento'] ?? '-' }}</td>
            <td> S/ {{ $item['Cuota'] ?? 0 }}</td>
            <td> {{ $item['DiasAtraso'] ?? 0 }}</td>
            <td> S/ {{ $item['Penalidad'] ?? 0 }}</td>
            <td>S/ {{ $item['Total'] ?? 0 }}</td>
            <td>S/ {{ $item['CuotaPagada'] ?? 0 }}</td>
            <td>S/ {{ $item['PenalPagada'] ?? 0 }}</td>
            <td>S/ {{ $item['MontoComp'] ?? 0 }}</td>
            {{--<td>S/ {{ $item['SaldoPendiente'] ?? 0 }}</td>--}}
        </tr>
        @endforeach
    </tbody>
</table>

@endsection