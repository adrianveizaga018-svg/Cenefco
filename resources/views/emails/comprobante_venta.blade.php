@extends('emails.layout')

@section('content')
    <h2>Comprobante de venta</h2>
    <p>Hola <strong>{{ $clienteNombre }}</strong>, adjuntamos el comprobante de tu compra.</p>

    <table>
        <tr>
            <th>Número</th>
            <td>{{ $venta->numeroVenta }}</td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td>{{ $venta->fechaVenta }}</td>
        </tr>
        <tr>
            <th>Estado</th>
            <td>{{ ucfirst($venta->estado) }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td><strong>Bs. {{ number_format($venta->total, 2) }}</strong></td>
        </tr>
    </table>

    <p>El comprobante completo está adjunto en formato PDF.</p>
    <p>Gracias por tu compra.</p>
@endsection
