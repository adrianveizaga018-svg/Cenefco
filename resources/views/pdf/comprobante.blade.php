<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    @page { margin: 0 0 50px 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #333333; }

    @php
      $colorEstado = match($venta->estado_pago) {
        'pagado'  => '#0F6E5C',
        'parcial' => '#B4690E',
        default   => '#A83232',
      };
    @endphp

    .contenido { padding: 0 46px; }

    /* ══ FRANJA SUPERIOR ══════════════════════════════════════ */
    .franja { width: 100%; height: 8px; background: #0D3B4F; }

    /* ══ ENCABEZADO ═══════════════════════════════════════════ */
    .encabezado {
      width: 100%; table-layout: fixed; border-collapse: collapse;
      padding: 22px 0 16px 0;
      border-bottom: 1px solid #dde3e7;
      margin-bottom: 20px;
    }
    .encabezado td { vertical-align: middle; }
    .encabezado .col-logo { width: 85px; }
    .encabezado .col-empresa { width: 45%; }
    .encabezado .col-num { width: 35%; text-align: right; }
    .logo { height: 42px; object-fit: contain; }
    .empresa-nombre { font-size: 14px; font-weight: bold; color: #0D3B4F; letter-spacing: 0.3px; }
    .empresa-sub { font-size: 8px; color: #8a8a8a; line-height: 1.5; margin-top: 2px; }
    .doc-tipo { font-size: 8px; color: #9a9a9a; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px; }
    .doc-num { font-size: 19px; font-weight: bold; color: #0D3B4F; }
    .doc-fecha { font-size: 8px; color: #9a9a9a; margin-top: 3px; }

    /* ══ ESTADO (línea simple, sin badge pill) ═══════════════ */
    .estado-linea {
      width: 100%; border-collapse: collapse; margin-bottom: 22px;
    }
    .estado-linea td { vertical-align: middle; }
    .estado-marca { width: 4px; background: {{ $colorEstado }}; }
    .estado-caja { padding: 10px 0 10px 14px; }
    .estado-nombre { font-size: 11px; font-weight: bold; color: {{ $colorEstado }}; text-transform: uppercase; letter-spacing: 0.8px; }
    .estado-detalle { font-size: 9px; color: #777; margin-top: 2px; }
    .estado-monto-cel { text-align: right; padding-right: 4px; }
    .estado-monto-label { font-size: 7.5px; color: #999; text-transform: uppercase; letter-spacing: 1px; }
    .estado-monto { font-size: 16px; font-weight: bold; color: #222; }

    /* ══ PROGRAMA ═════════════════════════════════════════════ */
    .programa-fila { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .programa-label { font-size: 8px; color: #999; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 5px; }
    .programa-nombre { font-size: 13.5px; font-weight: bold; color: #1a1a1a; line-height: 1.3; }
    .programa-meta { font-size: 9px; color: #777; margin-top: 5px; }
    .programa-meta b { color: #444; font-weight: bold; }

    /* ══ CUERPO 2 COLUMNAS (estudiante / referencia) ═════════ */
    .cuerpo { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .col-izq { width: 54%; vertical-align: top; padding-right: 20px; }
    .col-der { width: 46%; vertical-align: top; padding-left: 20px; border-left: 1px solid #eaeaea; }
    .seccion-titulo {
      font-size: 8px; font-weight: bold; color: #0D3B4F;
      text-transform: uppercase; letter-spacing: 1.2px;
      margin-bottom: 9px;
    }
    .nombre-est { font-size: 12.5px; font-weight: bold; color: #1a1a1a; margin-bottom: 7px; }
    .dato-row { border-collapse: collapse; font-size: 9.5px; width: 100%; }
    .dato-row td { padding: 3px 0; }
    .dato-label { color: #999; width: 78px; white-space: nowrap; }
    .dato-val { color: #333; }

    /* ══ TABLA DE CUOTAS ══════════════════════════════════════ */
    .titulo-seccion {
      font-size: 8px; font-weight: bold; color: #0D3B4F;
      text-transform: uppercase; letter-spacing: 1.2px;
      padding-bottom: 6px; border-bottom: 1.5px solid #0D3B4F;
      margin-bottom: 10px;
    }
    .tabla-cuotas { width: 100%; border-collapse: collapse; font-size: 9.5px; margin-bottom: 0; }
    .tabla-cuotas thead tr { background: #f4f6f7; }
    .tabla-cuotas th {
      padding: 7px 8px; text-align: left; font-weight: bold;
      color: #666; font-size: 8px; text-transform: uppercase; letter-spacing: 0.4px;
      border-bottom: 1px solid #dde3e7;
    }
    .tabla-cuotas th.right { text-align: right; }
    .tabla-cuotas td { padding: 6.5px 8px; border-bottom: 1px solid #f0f0f0; color: #444; }
    .monto-cuota { text-align: right; color: #777; }
    .monto-pagado { text-align: right; font-weight: bold; }

    /* ══ TOTALES ══════════════════════════════════════════════ */
    .tabla-totales { width: 100%; border-collapse: collapse; margin-top: 18px; }
    .col-totales-blank { width: 55%; }
    .col-totales { width: 45%; vertical-align: top; }
    .totales-caja { border: 1px solid #e3e7ea; border-radius: 3px; }
    .totales-caja table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .totales-caja td { padding: 8px 14px; }
    .totales-caja tr.fila-sep td { border-top: 1px solid #eaeaea; }
    .t-label { color: #888; }
    .t-val { text-align: right; color: #444; }
    .t-final td { padding-top: 9px; padding-bottom: 9px; background: #f7f9fa; }
    .t-final .t-label { color: #333; font-weight: bold; font-size: 10.5px; }
    .t-final .t-val { font-weight: bold; font-size: 13px; }

    /* ══ FIRMA ════════════════════════════════════════════════ */
    .bloque-firma { width: 100%; border-collapse: collapse; margin-top: 40px; }
    .firma-linea { width: 240px; border-top: 1px solid #999; padding-top: 6px; font-size: 9px; color: #444; }
    .firma-sub { font-size: 8px; color: #999; margin-top: 2px; }

    /* ══ PIE PÁGINA 1 ═════════════════════════════════════════ */
    .pie {
      position: fixed; bottom: -40px; left: 46px; right: 46px;
      padding-top: 10px; border-top: 1px solid #eee;
      display: table; width: 100%;
    }
    .pie-izq { display: table-cell; font-size: 8px; color: #bbb; }
    .pie-der { display: table-cell; text-align: right; font-size: 8px; color: #bbb; }

    /* ══ PÁGINA 2 — BIENVENIDA + REGLAMENTO ═══════════════════ */
    .page-break { page-break-before: always; }
    .encabezado2 {
      width: 100%; table-layout: fixed; border-collapse: collapse;
      border-bottom: 1px solid #dde3e7;
      margin-bottom: 22px; padding: 22px 0 14px 0;
    }
    .encabezado2 .col-logo { width: 80px; }
    .encabezado2 .col-empresa { width: 40%; }
    .encabezado2 .col-titulo { width: 45%; text-align: right; }
    .titulo-pag2 { font-size: 11px; font-weight: bold; color: #0D3B4F; text-transform: uppercase; letter-spacing: 0.5px; }
    .subtitulo-pag2 { font-size: 8px; color: #999; margin-top: 3px; }

    .carta-saludo { font-size: 10px; color: #0D3B4F; font-weight: bold; margin-bottom: 8px; }
    .carta-body { font-size: 9.5px; color: #444; line-height: 1.6; text-align: justify; margin-bottom: 8px; }

    .subtitulo-conducta {
      font-size: 8px; font-weight: bold; color: #0D3B4F;
      text-transform: uppercase; letter-spacing: 1.2px;
      padding-bottom: 6px; border-bottom: 1.5px solid #0D3B4F;
      margin: 20px 0 12px 0;
    }
    .regla-titulo { font-size: 9px; font-weight: bold; color: #0D3B4F; margin-bottom: 4px; }
    .regla-body { font-size: 9px; line-height: 1.5; color: #555; }
    .regla-body ul { margin: 0; padding-left: 13px; }
    .regla-body li { margin-bottom: 2px; }
    .regla-bloque { margin-bottom: 12px; }

    .declaracion {
      page-break-inside: avoid;
      margin-top: 18px; padding-top: 14px; border-top: 1px solid #dde3e7;
    }
    .decl-titulo {
      font-size: 8.5px; font-weight: bold; color: #0D3B4F;
      text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;
    }
    .decl-texto { font-size: 9px; color: #666; line-height: 1.6; text-align: justify; margin-bottom: 30px; }
    .firmas { width: 100%; border-collapse: collapse; }
    .firma-col { text-align: center; width: 46%; padding-top: 8px; border-top: 1px solid #999; font-size: 9px; color: #333; }
    .firma-sub2 { font-size: 8px; color: #999; margin-top: 3px; }
    .pie2 {
      position: fixed; bottom: -40px; left: 46px; right: 46px;
      padding-top: 10px; border-top: 1px solid #eee;
      display: table; width: 100%; font-size: 8px; color: #bbb;
    }
  </style>
</head>
<body>

<div class="franja"></div>
<div class="contenido">

{{-- ══ ENCABEZADO ════════════════════════════════════════════════ --}}
<table class="encabezado">
  <tr>
    <td class="col-logo" style="vertical-align:middle;">
      @php $logoPath = public_path('cenefco.png'); $logoAlt = public_path('logo-sm.png'); @endphp
      @if(file_exists($logoPath))
        <img src="{{ $logoPath }}" class="logo" alt="CENEFCO">
      @elseif(file_exists($logoAlt))
        <img src="{{ $logoAlt }}" class="logo" alt="CENEFCO">
      @else
        <div style="font-size:15px; font-weight:bold; color:#0D3B4F;">CENEFCO</div>
      @endif
    </td>
    <td class="col-empresa" style="vertical-align:middle; padding-left:12px;">
      <div class="empresa-nombre">CENEFCO</div>
      <div class="empresa-sub">Centro Nacional de Educación y Formación Continua &nbsp;·&nbsp; cenefco.com</div>
    </td>
    <td class="col-num" style="vertical-align:middle;">
      <div class="doc-tipo">Comprobante de Inscripción</div>
      <div class="doc-num">N.º {{ $venta->id_ins }}</div>
      <div class="doc-fecha">Emitido el {{ now()->format('d/m/Y') }}</div>
    </td>
  </tr>
</table>

{{-- ══ ESTADO ════════════════════════════════════════════════════ --}}
<table class="estado-linea">
  <tr>
    <td class="estado-marca">&nbsp;</td>
    <td class="estado-caja">
      <div class="estado-nombre">{{ strtoupper($estadoLabel) }}</div>
      <div class="estado-detalle">
        @if($venta->estado_pago === 'pagado')
          Cuenta cancelada en su totalidad
        @elseif($venta->estado_pago === 'parcial')
          Saldo pendiente por Bs. {{ number_format($venta->saldo_pendiente, 2) }}
        @else
          Aún no se registran pagos
        @endif
      </div>
    </td>
    <td class="estado-monto-cel">
      <div class="estado-monto-label">Total pagado</div>
      <div class="estado-monto">Bs. {{ number_format($venta->total_pagado, 2) }}</div>
    </td>
  </tr>
</table>

{{-- ══ PROGRAMA ══════════════════════════════════════════════════ --}}
<div class="programa-fila">
  <div class="programa-label">Programa</div>
  <div class="programa-nombre">{{ $venta->nombre_programa ?? '—' }}</div>
  <div class="programa-meta">
    Período <b>{{ $venta->periodo ?? '—' }}</b>
    &nbsp;&nbsp;·&nbsp;&nbsp;
    Gestión <b>{{ $venta->gestion ?? '—' }}</b>
    &nbsp;&nbsp;·&nbsp;&nbsp;
    Fecha de inscripción <b>{{ $venta->fecha_ins ?? '—' }}</b>
  </div>
</div>

{{-- ══ DATOS ESTUDIANTE + REFERENCIA ═══════════════════════════ --}}
<table class="cuerpo">
  <tr>
    <td class="col-izq">
      <div class="seccion-titulo">Recibimos de</div>
      <div class="nombre-est">{{ $venta->estudiante_nombre ?? '—' }}</div>
      <table class="dato-row">
        <tr><td class="dato-label">C.I.</td><td class="dato-val">{{ $venta->estudiante_ci ?? '—' }}</td></tr>
        @if($venta->estudiante_celular)
        <tr><td class="dato-label">Celular</td><td class="dato-val">{{ $venta->estudiante_celular }}</td></tr>
        @endif
        @if($venta->estudiante_email)
        <tr><td class="dato-label">Email</td><td class="dato-val">{{ $venta->estudiante_email }}</td></tr>
        @endif
      </table>
    </td>
    <td class="col-der">
      <div class="seccion-titulo">Referencia</div>
      <table class="dato-row">
        <tr><td class="dato-label">Inscripción</td><td class="dato-val">N.º {{ $venta->id_ins }}</td></tr>
        <tr><td class="dato-label">Período</td><td class="dato-val">{{ $venta->periodo ?? '—' }}</td></tr>
        <tr><td class="dato-label">Gestión</td><td class="dato-val">{{ $venta->gestion ?? '—' }}</td></tr>
        <tr><td class="dato-label">Fecha</td><td class="dato-val">{{ $venta->fecha_ins ?? '—' }}</td></tr>
      </table>
    </td>
  </tr>
</table>

{{-- ══ TABLA DE CUOTAS ══════════════════════════════════════════ --}}
<div class="titulo-seccion">Detalle de cuotas</div>

@if($venta->pagos->isEmpty())
  <div style="text-align:center; padding:18px; color:#bbb; font-style:italic; font-size:9.5px; background:#fafafa; margin-bottom:16px;">
    Sin pagos registrados.
  </div>
@else
  <table class="tabla-cuotas">
    <thead>
      <tr>
        <th>Cuota</th>
        <th class="right">Monto</th>
        <th>N.º boleta</th>
        <th>Fecha depósito</th>
        <th>Método</th>
        <th class="right">Pagado</th>
        <th>NIT / Razón social</th>
      </tr>
    </thead>
    <tbody>
      @foreach($venta->pagos as $p)
      <tr>
        <td style="color:#0D3B4F; font-weight:bold;">{{ $p->nro_pago ?? '—' }}.ª cuota</td>
        <td class="monto-cuota">{{ $p->monto_a_pagar ? 'Bs. ' . number_format((float)$p->monto_a_pagar, 2) : '—' }}</td>
        <td style="font-size:9px; color:#777;">{{ $p->nro_boleta_bancaria ?: '—' }}</td>
        <td>{{ $p->fecha_deposito ?: '—' }}</td>
        <td style="font-size:8.5px; color:#666;">{{ $metodosLabel[$p->metodo_pago ?? ''] ?? ($p->metodo_pago ?? '—') }}</td>
        <td class="monto-pagado" style="color:{{ $p->monto_pagado ? '#0F6E5C' : '#bbb' }};">
          {{ $p->monto_pagado ? 'Bs. ' . number_format((float)$p->monto_pagado, 2) : '—' }}
        </td>
        <td style="font-size:8.5px; color:#888;">
          @if($p->nro_nit || $p->nombre_nit)
            {{ $p->nro_nit ?? '' }}@if($p->nombre_nit) &nbsp;·&nbsp; {{ $p->nombre_nit }}@endif
          @else —
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
@endif

{{-- ══ TOTALES ══════════════════════════════════════════════════ --}}
<table class="tabla-totales">
  <tr>
    <td class="col-totales-blank">&nbsp;</td>
    <td class="col-totales">
      <div class="totales-caja">
        <table>
          <tr>
            <td class="t-label">Total del plan</td>
            <td class="t-val">Bs. {{ number_format($venta->total_a_pagar, 2) }}</td>
          </tr>
          <tr class="fila-sep">
            <td class="t-label">Total pagado</td>
            <td class="t-val" style="color:#0F6E5C; font-weight:bold;">Bs. {{ number_format($venta->total_pagado, 2) }}</td>
          </tr>
          <tr class="t-final fila-sep">
            <td class="t-label">Saldo pendiente</td>
            <td class="t-val" style="color:{{ $venta->saldo_pendiente == 0 ? '#0F6E5C' : '#A83232' }};">
              Bs. {{ number_format($venta->saldo_pendiente, 2) }}
            </td>
          </tr>
        </table>
      </div>
    </td>
  </tr>
</table>

{{-- ══ FIRMA ═══════════════════════════════════════════════════ --}}
<table class="bloque-firma">
  <tr>
    <td>
      <div class="firma-linea">
        <div style="font-weight:bold;">Secretaría Académica</div>
        <div class="firma-sub">CENEFCO · Sello y firma oficial</div>
      </div>
    </td>
  </tr>
</table>

</div>

{{-- ══ PIE PÁGINA 1 ═════════════════════════════════════════════ --}}
<table class="pie">
  <tr>
    <td class="pie-izq">REC-{{ $venta->id_ins }}-{{ now()->format('Ymd') }}</td>
    <td class="pie-der">CENEFCO · Conserve este comprobante</td>
  </tr>
</table>


{{-- ══ PÁGINA 2: BIENVENIDA + REGLAMENTO ═══════════════════════ --}}
<div class="page-break">
<div class="contenido">

  <table class="encabezado2">
    <tr>
      <td class="col-logo" style="vertical-align:middle;">
        @if(file_exists($logoPath))
        <img src="{{ $logoPath }}" style="height:40px; object-fit:contain;" alt="">
      @elseif(file_exists($logoAlt))
        <img src="{{ $logoAlt }}" style="height:40px; object-fit:contain;" alt="">
      @endif
      </td>
      <td class="col-empresa" style="vertical-align:middle; padding-left:12px;">
        <div style="font-size:12px; font-weight:bold; color:#0D3B4F;">CENEFCO</div>
        <div style="font-size:8px; color:#8a8a8a; margin-top:2px;">Centro Nacional de Educación y Formación Continua</div>
      </td>
      <td class="col-titulo" style="vertical-align:middle;">
        <div class="titulo-pag2">Bienvenida al programa</div>
        <div class="subtitulo-pag2">Reglamento e información importante</div>
      </td>
    </tr>
  </table>

  {{-- Carta de bienvenida --}}
  <div class="carta-saludo">Estimado/a {{ $venta->estudiante_nombre ?? 'Estudiante' }}:</div>
  <div class="carta-body">{{ str_replace('{programa}', '"' . ($venta->nombre_programa ?? 'el programa') . '"', $bienvenida) }}</div>
  <div class="carta-body">
    Su inscripción ha sido registrada con el N.º <strong>{{ $venta->id_ins }}</strong>
    para el período <strong>{{ $venta->periodo ?? '—' }} – Gestión {{ $venta->gestion ?? '—' }}</strong>.
    Le invitamos a leer el presente reglamento, el cual rige la convivencia académica y garantiza
    el aprovechamiento óptimo de su experiencia formativa.
  </div>

  <div class="subtitulo-conducta">Código de conducta</div>

  {{-- Código de conducta en 2 columnas --}}
  <table style="width:100%; border-collapse:collapse; vertical-align:top;">
    <tr>
      <td style="width:50%; vertical-align:top; padding-right:18px;">
        @foreach(array_slice($secciones, 0, 3) as $s)
          <div class="regla-bloque">
            <div class="regla-titulo">{{ $s['titulo'] }}</div>
            <div class="regla-body">
              <ul>
                @foreach($s['items'] as $item)
                  <li>{{ $item }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endforeach
      </td>
      <td style="width:50%; vertical-align:top; padding-left:18px; border-left:1px solid #eee;">
        @foreach(array_slice($secciones, 3, 3) as $s)
          <div class="regla-bloque">
            <div class="regla-titulo">{{ $s['titulo'] }}</div>
            <div class="regla-body">
              <ul>
                @foreach($s['items'] as $item)
                  <li>{{ $item }}</li>
                @endforeach
              </ul>
            </div>
          </div>
        @endforeach
      </td>
    </tr>
  </table>

  {{-- Declaración y firmas --}}
  <div class="declaracion">
    <div class="decl-titulo">Declaración de aceptación</div>
    <div class="decl-texto">
      Al inscribirse en el programa, el/la estudiante declara haber leído, comprendido y aceptado el presente
      reglamento, comprometiéndose a cumplirlo en su totalidad durante toda la duración del programa.
    </div>
    <table class="firmas">
      <tr>
        <td class="firma-col">
          <div><strong>{{ $venta->estudiante_nombre ?? '________________________________' }}</strong></div>
          <div class="firma-sub2">Firma del estudiante · C.I. {{ $venta->estudiante_ci ?? '—' }}</div>
        </td>
        <td style="width:8%;"></td>
        <td class="firma-col">
          <div><strong>Secretaría Académica</strong></div>
          <div class="firma-sub2">CENEFCO · Sello y firma oficial</div>
        </td>
      </tr>
    </table>
  </div>

</div>
</div>

{{-- Pie página 2 --}}
<table class="pie2">
  <tr>
    <td style="width:50%;">Inscripción N.º {{ $venta->id_ins }} · {{ now()->format('d/m/Y') }}</td>
    <td style="width:50%; text-align:right;">Documento con validez como constancia de inscripción y aceptación del reglamento</td>
  </tr>
</table>

</body>
</html>
