<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Informe de campaña — {{ $r['campana']['nombre'] }}</title>
<style>
    @page { margin: 28px 32px; }
    * { font-family: DejaVu Sans, sans-serif; }
    body { color: #0d2630; font-size: 11px; line-height: 1.45; }
    .marca { border-bottom: 3px solid #0b4258; padding-bottom: 8px; margin-bottom: 14px; }
    .marca .logo { height: 40px; }
    .marca td { vertical-align: middle; }
    .firma { margin-top: 46px; width: 100%; }
    .firma td { width: 50%; text-align: center; vertical-align: bottom; font-size: 10px; }
    .firma .linea { border-top: 1px solid #0d2630; margin: 0 30px; padding-top: 5px; }
    .firma .nombre { font-weight: bold; font-size: 11px; }
    .firma .cargo { color: #546b74; }
    .fuentes { margin-top: 14px; color: #546b74; font-size: 9px; }
    .marca .org { font-size: 10px; letter-spacing: 1.5px; color: #1a6080; font-weight: bold; text-transform: uppercase; }
    h1 { font-size: 18px; margin: 4px 0 2px; color: #0b4258; }
    .sub { color: #546b74; font-size: 10.5px; }
    h2 { font-size: 12.5px; color: #0b4258; margin: 16px 0 6px; border-bottom: 1px solid #d6e2e6; padding-bottom: 3px; }
    table { width: 100%; border-collapse: collapse; }
    .kpis td { width: 25%; padding: 8px 10px; background: #f2f7f9; border: 3px solid #fff; vertical-align: top; }
    .kpis .v { font-size: 16px; font-weight: bold; color: #0d2630; }
    .kpis .l { font-size: 9px; color: #546b74; text-transform: uppercase; letter-spacing: .5px; }
    .tabla th { text-align: left; font-size: 9px; text-transform: uppercase; color: #546b74; border-bottom: 1px solid #d6e2e6; padding: 4px 6px; }
    .tabla td { padding: 4px 6px; border-bottom: 1px solid #eef3f5; }
    .num { text-align: right; }
    .barra { height: 9px; background: #1a6080; }
    .flyers td { width: 33%; padding: 4px; text-align: center; vertical-align: top; }
    .flyers img { max-width: 100%; max-height: 190px; border: 1px solid #d6e2e6; }
    .pie { margin-top: 18px; border-top: 1px solid #d6e2e6; padding-top: 6px; color: #8aa0a8; font-size: 9px; }
    .aviso { color: #a35c00; font-size: 9.5px; }
</style>
</head>
<body>
    <div class="marca">
        <table>
            <tr>
                <td>@if ($logo)<img class="logo" src="{{ $logo }}" alt="CENEFCO">@endif</td>
                <td style="text-align:right"><div class="org">Informe de marketing</div><div class="sub">N.º {{ $numero }}</div></td>
            </tr>
        </table>
        <h1>{{ $titulo }}</h1>
        <div class="sub">
            Periodo: {{ \Carbon\Carbon::parse($r['periodo']['desde'])->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($r['periodo']['hasta'])->format('d/m/Y') }}
            @if ($r['campana']['programa']) · Programa: {{ $r['campana']['programa'] }} @endif
            @if ($r['campana']['responsable']) · Encargado: {{ $r['campana']['responsable'] }} @endif
        </div>
    </div>

    <table class="kpis">
        <tr>
            <td><div class="l">Gasto total</div><div class="v">Bs {{ number_format($r['gastos']['total'], 2) }}</div></td>
            <td><div class="l">Se unieron al grupo</div><div class="v">{{ $r['grupo']['nuevos'] }}</div><div class="l">Total del grupo: {{ $r['grupo']['total'] }}</div></td>
            <td><div class="l">Inscritos</div><div class="v">{{ $r['crm']['inscritos'] }}</div><div class="l">de {{ $r['crm']['interesados'] }} interesados</div></td>
            <td><div class="l">Cobrado</div><div class="v">Bs {{ number_format($r['crm']['cobrado'], 2) }}</div></td>
        </tr>
        <tr>
            <td><div class="l">Costo por persona en el grupo</div><div class="v">{{ $r['costos']['por_persona_grupo'] !== null ? 'Bs '.number_format($r['costos']['por_persona_grupo'], 2) : '—' }}</div></td>
            <td><div class="l">Costo por inscrito</div><div class="v">{{ $r['costos']['por_inscrito'] !== null ? 'Bs '.number_format($r['costos']['por_inscrito'], 2) : '—' }}</div></td>
            <td><div class="l">Retorno</div><div class="v">{{ $r['costos']['retorno'] !== null ? number_format($r['costos']['retorno'], 2).'x' : '—' }}</div><div class="l">cobrado por cada Bs invertido</div></td>
            <td><div class="l">Contactados por ventas</div><div class="v">{{ $r['crm']['contactados'] }}</div></td>
        </tr>
    </table>

    <h2>Gasto</h2>
    <table class="tabla">
        <tr><th>Concepto</th><th class="num">Monto</th></tr>
        <tr><td>Publicidad en Facebook / Instagram</td><td class="num">Bs {{ number_format($r['gastos']['facebook'], 2) }}</td></tr>
        <tr><td>Otros gastos (diseño, impresiones, etc.)</td><td class="num">Bs {{ number_format($r['gastos']['otros'], 2) }}</td></tr>
        <tr><td><b>Total</b></td><td class="num"><b>Bs {{ number_format($r['gastos']['total'], 2) }}</b></td></tr>
    </table>
    @if (! $r['facebook']['actualizado'])
        <p class="aviso">No hay datos de Facebook para este periodo: revisa que la campaña esté vinculada y sincronizada.</p>
    @else
        @if (\Carbon\Carbon::parse($r['facebook']['actualizado'])->lt(\Carbon\Carbon::parse($r['periodo']['hasta'])->subDays(2)))
            <p class="aviso">Los datos de Facebook llegan solo hasta el {{ \Carbon\Carbon::parse($r['facebook']['actualizado'])->format('d/m/Y') }}: la cuenta publicitaria no se está sincronizando, el gasto real del periodo puede ser mayor.</p>
        @endif
        <p class="sub">Facebook al {{ \Carbon\Carbon::parse($r['facebook']['actualizado'])->format('d/m/Y') }}: alcance {{ number_format($r['facebook']['alcance']) }} · impresiones {{ number_format($r['facebook']['impresiones']) }} · clics {{ number_format($r['facebook']['clics']) }} · resultados {{ number_format($r['facebook']['resultados']) }}.</p>
    @endif

    <h2>Crecimiento del grupo de WhatsApp</h2>
    @if (count($r['grupo']['dias']))
        @php $max = max(1, max(array_column($r['grupo']['dias'], 'nuevos'))); @endphp
        <table class="tabla">
            <tr><th style="width:18%">Fecha</th><th style="width:12%" class="num">Se unieron</th><th style="width:12%" class="num">Total</th><th></th></tr>
            @foreach ($r['grupo']['dias'] as $dia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($dia['fecha'])->format('d/m/Y') }}</td>
                    <td class="num">{{ $dia['nuevos'] }}</td>
                    <td class="num">{{ $dia['total'] }}</td>
                    <td><div class="barra" style="width: {{ round($dia['nuevos'] / $max * 100) }}%"></div></td>
                </tr>
            @endforeach
        </table>
    @else
        <p class="aviso">No hay registros diarios en este periodo.</p>
    @endif

    @if (count($flyers))
        <h2>Flyers publicados</h2>
        <table class="flyers">
            @foreach (array_chunk($flyers, 3) as $fila)
                <tr>
                    @foreach ($fila as $f)
                        <td><img src="{{ $f['src'] }}" alt="Flyer"><br><span class="sub">{{ \Carbon\Carbon::parse($f['fecha'])->format('d/m/Y') }}</span></td>
                    @endforeach
                    @for ($i = count($fila); $i < 3; $i++) <td></td> @endfor
                </tr>
            @endforeach
        </table>
    @endif

    @if (count($r['notas']))
        <h2>Notas del periodo</h2>
        <table class="tabla">
            @foreach ($r['notas'] as $n)
                <tr><td style="width:18%">{{ \Carbon\Carbon::parse($n['fecha'])->format('d/m/Y') }}</td><td>{{ $n['nota'] }}</td></tr>
            @endforeach
        </table>
    @endif

    <p class="fuentes"><b>De dónde salen los datos:</b> gasto, alcance, impresiones y clics de Facebook / Instagram vienen de Meta Ads (conexión automática);
        las personas que se unieron al grupo, del registro diario de marketing; interesados, contactados e inscritos, del embudo de ventas (CRM);
        y lo cobrado, de los pagos registrados en caja para esos inscritos.</p>

    <table class="firma">
        <tr>
            <td>
                <div class="linea">
                    <div class="nombre">{{ $autor }}</div>
                    <div class="cargo">{{ $cargo ?: 'Marketing' }}</div>
                    <div class="cargo">Elaborado el {{ $generado->format('d/m/Y') }}</div>
                </div>
            </td>
            <td>
                <div class="linea">
                    <div class="nombre">&nbsp;</div>
                    <div class="cargo">Revisado por Dirección</div>
                    <div class="cargo">Fecha: ____/____/________</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="pie">Generado el {{ $generado->format('d/m/Y H:i') }} por {{ $autor }} · Sistema CENEFCO. Este informe queda guardado en el historial de la campaña.</div>
</body>
</html>
