<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; background: #fff; }

        .header { background: #1a1a2e; color: #fff; padding: 20px 30px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header h1 { font-size: 22px; letter-spacing: 1px; }
        .header .subtitle { font-size: 11px; color: #aaa; margin-top: 3px; }
        .header .numero { font-size: 18px; font-weight: bold; color: #fff; }
        .header .numero-label { font-size: 10px; color: #aaa; }

        .estado-badge {
            display: inline-block;
            border-radius: 4px;
            padding: 3px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .estado-pendiente  { background: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
        .estado-completada { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .estado-anulada    { background: #fce4ec; color: #c62828; border: 1px solid #ef9a9a; }

        .tipo-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
            border-radius: 4px;
            padding: 2px 10px;
            font-size: 10px;
        }

        .section { padding: 15px 30px; }
        .section-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 3px 0; font-size: 12px; vertical-align: top; }
        .info-grid .label { color: #888; width: 130px; }

        table.items { width: 100%; border-collapse: collapse; }
        table.items thead tr { background: #1a1a2e; }
        table.items th { padding: 8px 10px; text-align: left; font-size: 10px; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; }
        table.items th.right, table.items td.right { text-align: right; }
        table.items td { padding: 7px 10px; border-bottom: 1px solid #f0f0f0; font-size: 12px; }
        table.items tbody tr:nth-child(even) td { background: #fafafa; }
        table.items tbody tr:last-child td { border-bottom: none; }

        .totals { padding: 5px 30px 20px; }
        .totals table { width: 300px; margin-left: auto; border-collapse: collapse; }
        .totals td { padding: 4px 8px; font-size: 12px; }
        .totals .sep td { border-top: 1px solid #ddd; }
        .totals .total-row td { font-size: 15px; font-weight: bold; color: #1a1a2e; padding-top: 6px; border-top: 2px solid #1a1a2e; }
        .totals td.right { text-align: right; }

        .obs { padding: 0 30px 15px; }
        .obs-box { background: #f9f9f9; border-left: 3px solid #1a1a2e; padding: 8px 12px; font-size: 12px; color: #444; }

        .footer { background: #f5f5f5; padding: 12px 30px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; margin-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <h1>{{ config('bot.negocio.nombre', 'PuntoVentas') }}</h1>
        <p class="subtitle">Comprobante de Venta</p>
    </div>
    <div class="header-right">
        <p class="numero-label">N° DE VENTA</p>
        <p class="numero">{{ $venta->numero_venta }}</p>
    </div>
</div>

<div class="section">
    <p class="section-title">Información de la Venta</p>
    <table class="info-grid">
        <tr>
            <td class="label">Fecha:</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
            <td class="label">Hora:</td>
            <td>{{ $venta->hora_venta }}</td>
        </tr>
        <tr>
            <td class="label">Tipo de venta:</td>
            <td><span class="tipo-badge">{{ ucfirst($venta->tipo_venta ?? 'contado') }}</span></td>
            <td class="label">Estado:</td>
            <td>
                @php $estado = $venta->estado; @endphp
                <span class="estado-badge estado-{{ $estado }}">{{ ucfirst($estado) }}</span>
            </td>
        </tr>
        <tr>
            <td class="label">Cliente:</td>
            <td colspan="3">
                <strong>{{ $venta->cliente->razon_social ?? $venta->cliente->nombre ?? 'Consumidor final' }}</strong>
                @if($venta->cliente?->nit_ci)
                    &nbsp;·&nbsp; NIT/CI: {{ $venta->cliente->nit_ci }}
                @endif
            </td>
        </tr>
        @if($venta->cliente?->celular)
        <tr>
            <td class="label">Teléfono:</td>
            <td colspan="3">{{ $venta->cliente->celular }}</td>
        </tr>
        @endif
        @if($venta->cliente?->email)
        <tr>
            <td class="label">Email:</td>
            <td colspan="3">{{ $venta->cliente->email }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="section" style="padding-top: 0;">
    <p class="section-title">Detalle de Productos</p>
    <table class="items">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Producto</th>
                <th class="right" style="width:60px;">Cant.</th>
                <th class="right" style="width:90px;">P. Unit.</th>
                <th class="right" style="width:80px;">Desc.</th>
                <th class="right" style="width:70px;">IVA</th>
                <th class="right" style="width:90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $i => $detalle)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                <td class="right">{{ $detalle->cantidad }}</td>
                <td class="right">Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="right">Bs. {{ number_format($detalle->descuento, 2) }}</td>
                <td class="right">Bs. {{ number_format($detalle->iva, 2) }}</td>
                <td class="right">Bs. {{ number_format($detalle->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="totals">
    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">Bs. {{ number_format($venta->subtotal, 2) }}</td>
        </tr>
        @if($venta->descuento > 0)
        <tr>
            <td>Descuento</td>
            <td class="right">- Bs. {{ number_format($venta->descuento, 2) }}</td>
        </tr>
        @endif
        <tr class="sep">
            <td>Sin IVA</td>
            <td class="right">Bs. {{ number_format($venta->total_sin_iva, 2) }}</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td class="right">Bs. {{ number_format($venta->total_iva, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL</td>
            <td class="right">Bs. {{ number_format($venta->total, 2) }}</td>
        </tr>
    </table>
</div>

@if($venta->observaciones)
<div class="obs">
    <p class="section-title" style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:#888; border-bottom:1px solid #eee; padding-bottom:4px; margin-bottom:8px;">Observaciones</p>
    <div class="obs-box">{{ $venta->observaciones }}</div>
</div>
@endif

<div class="footer">
    <p>{{ config('bot.negocio.nombre', 'PuntoVentas') }} &nbsp;·&nbsp; Este documento es un comprobante interno de venta.</p>
    <p style="margin-top:3px;">Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
</div>

</body>
</html>
