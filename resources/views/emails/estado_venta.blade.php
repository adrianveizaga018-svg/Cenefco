@extends('emails.layout')

@section('content')
    @php
        $iconos = [
            'pagado'    => '✅',
            'anulado'   => '❌',
            'parcial'   => '⏳',
            'pendiente' => '🟡',
        ];
        $icono = $iconos[$venta->estado] ?? '📋';
    @endphp

    <h2>{{ $icono }} Actualización de tu pedido</h2>
    <p>Hola <strong>{{ $clienteNombre }}</strong>, el estado de tu pedido ha cambiado.</p>

    <table>
        <tr>
            <th>Número de pedido</th>
            <td>{{ $venta->numeroVenta }}</td>
        </tr>
        <tr>
            <th>Estado anterior</th>
            <td>{{ ucfirst($estadoAnterior) }}</td>
        </tr>
        <tr>
            <th>Estado actual</th>
            <td><strong>{{ ucfirst($venta->estado) }}</strong></td>
        </tr>
        <tr>
            <th>Total</th>
            <td>Bs. {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>

    @if($venta->estado === 'anulado')
        <p style="color: #e53e3e;">
            Tu pedido ha sido anulado. Si tienes dudas, contáctanos.
        </p>
    @elseif($venta->estado === 'pagado')
        <p style="color: #38a169;">
            ¡Tu pago fue confirmado! Gracias por tu compra.
        </p>
    @endif
@endsection
