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
        table.lista td { padding: 6px 10px; border-bottom: 1px solid #f0f0f0; font-size: 11px; }
        table.lista tbody tr:nth-child(even) td { background: #fafafa; }
        table.lista tbody tr:last-child td { border-bottom: none; }
        .num { color: #aaa; width: 36px; }

        .footer { margin-top: 20px; padding: 10px 28px; border-top: 1px solid #eee; font-size: 10px; color: #aaa; display: table; width: 100%; }
        .footer-left  { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <div class="subtitle">CENEFCO — Cursos Migrados</div>
        <h1>{{ $curso->nombre }}</h1>
    </div>
    <div class="header-right">
        <div class="badge">{{ $curso->participantes_count }} participantes</div>
    </div>
</div>

<div class="meta">
    <table>
        <tr>
            <td class="label">Período</td>
            <td class="value">{{ $curso->periodo ?? '—' }}</td>
            <td class="label">Gestión</td>
            <td class="value">{{ $curso->gestion ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">URL del curso</td>
            <td class="value" colspan="3">{{ $curso->url }}</td>
        </tr>
        <tr>
            <td class="label">Fecha inicio</td>
            <td class="value">{{ $curso->fecha_inicio ?? '—' }}</td>
            <td class="label">Generado</td>
            <td class="value">{{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <div class="section-title">Lista de participantes</div>
    <table class="lista">
        <thead>
            <tr>
                <th class="num">#</th>
                <th>Nombre completo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participantes as $i => $p)
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $p->nombre_completo }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <div class="footer-left">cenefco.com — Registro histórico de cursos migrados</div>
    <div class="footer-right">Total: {{ count($participantes) }} participantes</div>
</div>

</body>
</html>
