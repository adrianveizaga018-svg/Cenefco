<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; background: #fff; }

        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.04;
            width: 280px;
            height: 280px;
        }

        .header { background: #1a1a2e; color: #fff; padding: 16px 28px; display: table; width: 100%; }
        .header-left  { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .header h1    { font-size: 15px; letter-spacing: 0.5px; margin-bottom: 3px; }
        .header .subtitle { font-size: 10px; color: #aaa; }

        .summary { padding: 10px 28px; background: #f7f8fa; border-bottom: 1px solid #e0e0e0; display: table; width: 100%; }
        .summary-cell { display: table-cell; }
        .summary-cell + .summary-cell { border-left: 1px solid #ddd; padding-left: 20px; margin-left: 20px; }
        .summary .num  { font-size: 20px; font-weight: bold; color: #1a1a2e; }
        .summary .lbl  { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        .section { padding: 14px 28px 0; }
        .section-title {
            font-size: 9px; text-transform: uppercase; letter-spacing: 1px;
            color: #888; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;
        }

        table.lista { width: 100%; border-collapse: collapse; }
        table.lista thead tr { background: #1a1a2e; }
        table.lista th { padding: 7px 10px; text-align: left; font-size: 9px; color: #fff; text-transform: uppercase; letter-spacing: 0.5px; }
        table.lista td { padding: 5px 10px; border-bottom: 1px solid #f0f0f0; font-size: 10px; }
        table.lista tbody tr:nth-child(even) td { background: #fafafa; }
        table.lista tbody tr:last-child td { border-bottom: none; }
        .num-col  { color: #aaa; width: 36px; }
        .count    { font-weight: bold; color: #1a1a2e; text-align: center; }
        .periodo  { font-size: 9px; background: #eef2ff; color: #3730a3; border-radius: 3px; padding: 1px 6px; display: inline-block; }
        .dash     { color: #ccc; }

        .footer { margin-top: 18px; padding: 8px 28px; border-top: 1px solid #eee; font-size: 9px; color: #aaa; display: table; width: 100%; }
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
        <div class="subtitle">CENEFCO — Registro Histórico</div>
        <h1>Listado General de Cursos Migrados</h1>
    </div>
    <div class="header-right">
        <div style="font-size:10px; color:#aaa;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>

<div class="summary">
    <div class="summary-cell" style="padding: 6px 28px 6px 0;">
        <div class="num">{{ $totalCursos }}</div>
        <div class="lbl">Cursos migrados</div>
    </div>
    <div class="summary-cell" style="padding: 6px 0 6px 28px;">
        <div class="num">{{ $totalParticipantes }}</div>
        <div class="lbl">Total participantes</div>
    </div>
</div>

<div class="section">
    <div class="section-title">Detalle por curso</div>
    <table class="lista">
        <thead>
            <tr>
                <th class="num-col">#</th>
                <th>Nombre del Curso</th>
                <th>Período</th>
                <th>Gestión</th>
                <th style="text-align:center;">Participantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cursos as $i => $curso)
            <tr>
                <td class="num-col">{{ $i + 1 }}</td>
                <td>{{ $curso->nombre }}</td>
                <td>
                    @if($curso->periodo)
                        <span class="periodo">{{ $curso->periodo }}</span>
                    @else
                        <span class="dash">—</span>
                    @endif
                </td>
                <td>{{ $curso->gestion ?? '—' }}</td>
                <td class="count">{{ $curso->participantes_count ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <div class="footer-left">cenefco.com — Cursos Migrados</div>
    <div class="footer-right">Total participantes: <strong>{{ $totalParticipantes }}</strong></div>
</div>

</body>
</html>
