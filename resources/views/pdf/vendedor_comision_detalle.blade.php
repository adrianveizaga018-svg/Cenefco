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

        .summary { padding: 10px 24px; background: #f7f8fa; border-bottom: 1px solid #e0e0e0; display: table; width: 100%; }
        .summary .box { display: table-cell; padding-right: 28px; }
        .summary .num  { font-size: 16px; font-weight: bold; color: #1a1a2e; }
        .summary .lbl  { font-size: 8px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { padding: 12px 24px 0; }

        table.lista { width: 100%; border-collapse: collapse; }
        table.lista thead tr { background: #1a1a2e; }
        table.lista th { padding: 6px 6px; text-align: left; font-size: 8px; color: #fff; text-transform: uppercase; letter-spacing: 0.3px; }
        table.lista td { padding: 4px 6px; border-bottom: 1px solid #f0f0f0; font-size: 9px; }
        table.lista tbody tr:nth-child(even) td { background: #fafafa; }
        table.lista tbody tr:last-child td { border-bottom: none; }
        table.lista tfoot td { padding: 6px 6px; font-size: 9px; font-weight: bold; border-top: 2px solid #1a1a2e; }
        .num-col  { color: #aaa; width: 24px; }
        .money { text-align: right; }
        .center { text-align: center; }

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
        <div class="subtitle">CENEFCO — Gestión Comercial</div>
        <h1>Reporte de Comisión Estimada por Vendedor</h1>
    </div>
    <div class="header-right">
        <div style="font-size:10px; color:#aaa;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="summary">
    <div class="box">
        <div class="num">{{ $detalle->vendedor_nombre }}</div>
        <div class="lbl">Vendedor</div>
    </div>
    <div class="box">
        <div class="num">Bs. {{ number_format($detalle->total_comision, 2) }}</div>
        <div class="lbl">Comisión estimada total</div>
    </div>
</div>

<div class="section">
    <table class="lista">
        <thead>
            <tr>
                <th class="num-col">#</th>
                <th>Curso</th>
                <th>Categoría</th>
                <th class="center">Inscritos</th>
                <th class="money">Comisión / inscrito</th>
                <th class="money">Comisión estimada</th>
            </tr>
        </thead>
        <tbody>
            @forelse($detalle->cursos as $i => $curso)
            <tr>
                <td class="num-col">{{ $i + 1 }}</td>
                <td>{{ $curso->nombre_programa }}</td>
                <td>{{ $curso->categoria_nombre ?? '(Sin categoría)' }}</td>
                <td class="center">{{ $curso->total_inscritos }}</td>
                <td class="money">Bs. {{ number_format($curso->comision_monto, 2) }}</td>
                <td class="money">Bs. {{ number_format($curso->comision_estimada, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="center" style="padding: 16px; color: #999;">Este vendedor no tiene cursos asociados.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td class="money">Bs. {{ number_format($detalle->total_comision, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="footer">
    <div class="footer-left">cenefco.com — Reporte de comisión estimada (no liquidada)</div>
    <div class="footer-right">Total de cursos: <strong>{{ count($detalle->cursos) }}</strong></div>
</div>

</body>
</html>
