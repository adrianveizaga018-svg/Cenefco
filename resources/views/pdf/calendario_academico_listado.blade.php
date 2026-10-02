<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #222; background: #fff; }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 280px;
            height: 280px;
        }

        .header { background: #1a1a2e; color: #fff; padding: 14px 24px; display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header h1    { font-size: 15px; letter-spacing: 0.5px; margin-bottom: 3px; }
        .header .subtitle { font-size: 10px; color: #aaa; }

        .summary { padding: 8px 24px; background: #f7f8fa; border-bottom: 1px solid #e0e0e0; }
        .summary .num  { font-size: 18px; font-weight: bold; color: #1a1a2e; }
        .summary .lbl  { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { padding: 12px 24px 0; }

        table.lista { width: 100%; border-collapse: collapse; }
        table.lista thead tr { background: #1a1a2e; }
        table.lista th { padding: 6px 6px; text-align: left; font-size: 8px; color: #fff; text-transform: uppercase; letter-spacing: 0.3px; }
        table.lista td { padding: 4px 6px; border-bottom: 1px solid #f0f0f0; font-size: 9px; }
        table.lista tbody tr:nth-child(even) td { background: #fafafa; }
        table.lista tbody tr:last-child td { border-bottom: none; }
        .num-col  { color: #aaa; width: 24px; }
        .dash     { color: #ccc; }
        .tipo-badge {
            font-size: 8px; background: #eef2ff; color: #3730a3; border-radius: 3px;
            padding: 1px 5px; display: inline-block; white-space: nowrap;
        }
        .money { text-align: right; }

        .footer { margin-top: 14px; padding: 8px 24px; border-top: 1px solid #eee; font-size: 9px; color: #aaa; display: table; width: 100%; }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

@php
    $logoPath = public_path('logo-sm.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp

@if($logoBase64)
<img class="watermark" src="{{ $logoBase64 }}" alt="" />
@endif

<div class="header">
    <div class="header-left">
        <div class="subtitle">CENEFCO — Estructura Académica</div>
        <h1>Cronograma / Calendario Académico</h1>
    </div>
    <div class="header-right">
        <div style="font-size:10px; color:#aaa;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="summary">
    <div class="num">{{ $total }}</div>
    <div class="lbl">Eventos en el reporte</div>
</div>

<div class="section">
    <table class="lista">
        <thead>
            <tr>
                <th class="num-col">#</th>
                <th>Tipo</th>
                <th>Título</th>
                <th>Programa</th>
                <th>Vendedor</th>
                <th>Página</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Duración</th>
                <th>Costo Inflado</th>
                <th>Descuento</th>
                <th>Precio VIP</th>
                <th>Público</th>
            </tr>
        </thead>
        <tbody>
            @foreach($eventos as $i => $ev)
            <tr>
                <td class="num-col">{{ $i + 1 }}</td>
                <td><span class="tipo-badge">{{ $tipoLabels[$ev->tipo] ?? ($ev->tipo ?? '—') }}</span></td>
                <td>{{ $ev->titulo }}</td>
                <td>{{ $ev->nombre_programa ?? '—' }}</td>
                <td>{{ $ev->vendedor_nombre ?? '—' }}</td>
                <td>{{ $ev->pagina ?? '—' }}</td>
                <td>{{ $ev->fecha_inicio ? substr($ev->fecha_inicio, 0, 10) : '—' }}</td>
                <td>{{ $ev->fecha_fin ? substr($ev->fecha_fin, 0, 10) : '—' }}</td>
                <td>{{ $ev->duracion_dias ?? '—' }}</td>
                <td class="money">{{ $ev->costo_inflado !== null ? 'Bs. ' . number_format($ev->costo_inflado, 2) : '—' }}</td>
                <td class="money">{{ $ev->descuento !== null ? 'Bs. ' . number_format($ev->descuento, 2) : '—' }}</td>
                <td class="money">{{ $ev->precio_vip !== null ? 'Bs. ' . number_format($ev->precio_vip, 2) : '—' }}</td>
                <td>{{ $ev->publico ? 'Sí' : 'No' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <div class="footer-left">cenefco.com — Calendario Académico</div>
    <div class="footer-right">Total de eventos: <strong>{{ $total }}</strong></div>
</div>

</body>
</html>
