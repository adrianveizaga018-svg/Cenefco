<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; background: #fff; }

        .header { background: #1a1a2e; color: #fff; padding: 20px 30px; }
        .header h1 { font-size: 22px; letter-spacing: 1px; }
        .header p { font-size: 11px; color: #aaa; margin-top: 3px; }

        .badge {
            display: inline-block;
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
            border-radius: 4px;
            padding: 2px 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .section { padding: 18px 30px; }
        .section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid { width: 100%; }
        .info-grid td { padding: 3px 0; font-size: 12px; }
        .info-grid .label { color: #888; width: 140px; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead tr { background: #f5f5f5; }
        table.items th { padding: 8px 10px; text-align: left; font-size: 11px; color: #555; }
        table.items th.right, table.items td.right { text-align: right; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
        table.items tbody tr:last-child td { border-bottom: none; }

        .totals { padding: 10px 30px 20px; }
        .totals table { width: 100%; }
        .totals td { padding: 4px 0; font-size: 12px; }
        .totals .total-row td { font-size: 15px; font-weight: bold; color: #1a1a2e; padding-top: 8px; border-top: 2px solid #1a1a2e; }
        .totals td.right { text-align: right; }

        .footer { background: #f9f9f9; padding: 15px 30px; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; }

        .channel-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 3px;
            padding: 1px 8px;
            font-size: 10px;
        }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <h1>{{ config('bot.negocio.nombre', 'PuntoVentas') }}</h1>
    <p>Comprobante de Pedido &nbsp;·&nbsp; <span class="channel-badge">WhatsApp</span></p>
</div>

{{-- INFO PEDIDO + CLIENTE --}}
<div class="section">
    <p class="section-title">Información del Pedido</p>
    <table class="info-grid">
        <tr>
            <td class="label">Número de pedido:</td>
            <td><strong>{{ $venta->numero_venta }}</strong></td>
            <td class="label">Estado:</td>
            <td><span class="badge">{{ ucfirst($venta->estado) }}</span></td>
        </tr>
        <tr>
            <td class="label">Fecha:</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
            <td class="label">Hora:</td>
            <td>{{ $venta->hora_venta }}</td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td colspan="3">{{ $venta->cliente->razon_social ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono:</td>
            <td colspan="3">{{ $venta->cliente->celular ?? '-' }}</td>
        </tr>
    </table>
</div>

{{-- PRODUCTOS --}}
<div class="section">
    <p class="section-title">Detalle de Productos</p>
    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th class="right">Cant.</th>
                <th class="right">P. Unit.</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $i => $detalle)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                <td class="right">{{ $detalle->cantidad }}</td>
                <td class="right">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="right">Bs. {{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- TOTALES --}}
<div class="totals">
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">Bs. {{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Descuento</td>
            <td class="right">Bs. {{ number_format($venta->descuento, 2) }}</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td class="right">Bs. {{ number_format($venta->total_iva, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL A PAGAR</td>
            <td class="right">Bs. {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>
</div>

{{-- OBSERVACIONES --}}
@if($venta->observaciones)
<div class="section" style="padding-top: 0;">
    <p class="section-title">Observaciones</p>
    <p>{{ $venta->observaciones }}</p>
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <p>Un asesor se comunicará contigo para coordinar el pago y la entrega.</p>
    <p style="margin-top:4px;">Este documento es un comprobante de pedido, no una factura oficial.</p>
</div>

</body>
</html>
