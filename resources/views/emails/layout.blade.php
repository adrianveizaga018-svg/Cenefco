{{--
    Plantilla base de todos los correos de CENEFCO.
    Hecha con tablas y estilos en línea para que se vea igual en Gmail, Outlook y móviles.
    El logo va incrustado en el correo (CID), así se muestra aunque el cliente bloquee imágenes externas.
--}}
@php
    $logoPath = resource_path('email/cenefco-logo.png');
    $logoSrc  = (isset($message) && $message instanceof \Illuminate\Mail\Message)
        ? $message->embed($logoPath)
        : 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)); // vista previa
    $marca = config('mail.from.name', 'CENEFCO');
    $franja = ['#F7931E', '#009A4E', '#C8AE8E', '#0A84FF', '#C61AFF', '#00D8E6'];
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>{{ $titulo ?? $marca }}</title>
    <style>
        /* Clases que usan las plantillas de comprobantes */
        .btn { display: inline-block; padding: 14px 32px; background: #0B4258; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 15px; }
        table.datos { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .contenido table th { background: #F1F5F7; padding: 10px 12px; text-align: left; font-size: 13px; color: #334155; }
        .contenido table td { padding: 10px 12px; border-bottom: 1px solid #E8EEF1; font-size: 13px; color: #1E293B; }
        /* Tablas de maquetación de los correos nuevos: sin las líneas de las tablas de datos */
        .contenido table.limpia td { border-bottom: 0 !important; }
        @media (max-width: 620px) {
            .tarjeta { width: 100% !important; border-radius: 0 !important; }
            .relleno { padding-left: 22px !important; padding-right: 22px !important; }
        }
    </style>
</head>
<body style="margin:0; padding:0; background:#EEF2F4; font-family: 'Segoe UI', Arial, Helvetica, sans-serif;">
    @isset($preheader)
        {{-- Texto que Gmail muestra junto al asunto en la bandeja --}}
        <div style="display:none; max-height:0; overflow:hidden; opacity:0;">{{ $preheader }}</div>
    @endisset

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#EEF2F4;">
        <tr>
            <td align="center" style="padding: 28px 12px;">
                <table role="presentation" class="tarjeta" width="600" cellpadding="0" cellspacing="0"
                       style="width:600px; max-width:600px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 8px 30px rgba(7,45,58,0.12);">

                    {{-- Encabezado con logo --}}
                    <tr>
                        <td align="center" style="background:#0B4258; background-image:linear-gradient(135deg,#072D3A 0%,#0B4258 55%,#1A6080 100%); padding: 26px 24px 20px;">
                            <img src="{{ $logoSrc }}" alt="{{ $marca }} — Centro Nacional de Educación y Formación Continua"
                                 width="250" style="display:block; width:250px; max-width:80%; height:auto; border:0;">
                        </td>
                    </tr>

                    {{-- Franja con los colores del logo --}}
                    <tr>
                        <td style="padding:0; line-height:0; font-size:0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    @foreach ($franja as $color)
                                        <td height="5" style="height:5px; background:{{ $color }};"></td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Contenido --}}
                    <tr>
                        <td class="relleno contenido" style="padding: 36px 44px 30px; color:#1E293B; font-size:15px; line-height:1.65;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pie --}}
                    <tr>
                        <td class="relleno" style="background:#F6F8F9; border-top:1px solid #E8EEF1; padding: 22px 44px; text-align:center;">
                            <p style="margin:0 0 4px; font-size:13px; font-weight:bold; color:#0B4258;">{{ $marca }}</p>
                            <p style="margin:0 0 10px; font-size:12px; color:#64748B;">Centro Nacional de Educación y Formación Continua</p>
                            <p style="margin:0; font-size:11px; color:#94A3B8;">
                                Este correo se envió automáticamente, por favor no lo respondas.<br>
                                Si no esperabas este mensaje, puedes ignorarlo.
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:16px 0 0; font-size:11px; color:#94A3B8;">© {{ date('Y') }} {{ $marca }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
