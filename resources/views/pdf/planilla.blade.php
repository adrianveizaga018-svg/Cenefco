<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; background: #fff; }

        .header { background: #1a1a2e; color: #fff; padding: 18px 28px; display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header h1 { font-size: 16px; letter-spacing: 0.5px; margin-bottom: 3px; }
        .header .subtitle { font-size: 10px; color: #aaa; }
        .header .badge { font-size: 10px; background: #3b4a6b; color: #c8d6f0; border-radius: 4px; padding: 3px 10px; display: inline-block; }

        .meta { padding: 14px 28px; background: #f7f8fa; border-bottom: 1px solid #e8e8e8; }
        .meta table { width: 100%; border-collapse: collapse; }
        .meta td { padding: 3px 0; font-size: 11px; vertical-align: top; }
        .meta .label { color: #888; width: 120px; text-transform: uppercase; font-size: 10px; letter-spacing: 0.5px; }
        .meta .value { color: #222; font-weight: bold; }

        .section { padding: 14px 28px 0; }
        .section-title {
            font-size: 10px; text-transform: uppercase; letter-spacing: 1px;
            color: #888; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;
        }

        table.lista { width: 100%; border-collapse: collapse; }
        table.lista thead tr { background: #1a1a2e; }
        table.lista th { padding: 7px 10px; text-align: left; font-size: 10px; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; }
        table.lista th.right { text-align: right; }
        table.lista td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
        table.lista td.right { text-align: right; font-family: 'Courier New', monospace; }
        table.lista tbody tr:nth-child(even) td { background: #fafafa; }
        table.lista tbody tr:last-child td { border-bottom: none; }
        .num { color: #aaa; width: 36px; }

        .tabla-totales { width: 100%; margin-top: 16px; }
        .tabla-totales td { padding: 4px 0; }
        .col-totales { width: 260px; float: right; }
        .col-totales table { border-collapse: collapse; font-size: 11px; width: 100%; }
        .col-totales tr { border-top: 1px solid #eee; }
        .col-totales td.label { color: #999; padding: 6px 0; }
        .col-totales td.value { text-align: right; font-family: 'Courier New', monospace; font-weight: bold; color: #1a7a3f; font-size: 13px; padding: 6px 0; }

        .footer { margin-top: 24px; padding: 10px 28px; border-top: 1px solid #eee; font-size: 10px; color: #aaa; display: table; width: 100%; }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }

        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <div class="subtitle">CENEFCO — Planilla de Sueldos</div>
        <h1>Planilla {{ $nombreMes }} {{ $planilla->anio }}</h1>
    </div>
    <div class="header-right">
        <div class="badge">{{ count($planilla->detalle) }} empleado(s)</div>
    </div>
</div>

<div class="meta">
    <table>
        <tr>
            <td class="label">Período</td>
            <td class="value">{{ $nombreMes }} {{ $planilla->anio }}</td>
            <td class="label">Generado</td>
            <td class="value">{{ $planilla->created_at ? \Carbon\Carbon::parse($planilla->created_at)->format('d/m/Y H:i') : '—' }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Detalle por empleado</div>
    <table class="lista">
        <thead>
            <tr>
                <th class="num">#</th>
                <th>Nombre completo</th>
                <th>Cargo</th>
                <th class="right">Sueldo base</th>
                <th class="right">Descuentos</th>
                <th class="right">Bonos</th>
                <th class="right">Neto pagado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($planilla->detalle as $i => $d)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $d->nombre_completo }}</td>
                <td>{{ $d->cargo }}</td>
                <td class="right">Bs. {{ number_format($d->monto_base ?? $d->monto, 2) }}</td>
                <td class="right">{{ $d->total_descuentos > 0 ? '-Bs. '.number_format($d->total_descuentos, 2) : '—' }}</td>
                <td class="right">{{ $d->total_bonos > 0 ? '+Bs. '.number_format($d->total_bonos, 2) : '—' }}</td>
                <td class="right">Bs. {{ number_format($d->monto, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="col-totales">
            <table>
                <tr>
                    <td class="label">Total planilla:</td>
                    <td class="value">Bs. {{ number_format($planilla->total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div class="footer">
    <div class="footer-left">cenefco.com — Planilla de sueldos de planta</div>
    <div class="footer-right">Total: {{ count($planilla->detalle) }} empleado(s) — Bs. {{ number_format($planilla->total, 2) }}</div>
</div>

</body>
</html>
