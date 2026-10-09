@extends('emails.layout', [
    'titulo'    => 'Recupera tu contraseña',
    'preheader' => 'Usa este enlace para crear una nueva contraseña. Vence en 60 minutos.',
])

@section('content')
    <p style="margin:0 0 6px; font-size:13px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#1A6080;">
        Recuperación de contraseña
    </p>
    <h1 style="margin:0 0 14px; font-size:26px; line-height:1.25; color:#072D3A;">
        Hola, {{ $user->nombre }}
    </h1>

    <p style="margin:0 0 22px;">
        Recibimos una solicitud para cambiar la contraseña de tu cuenta <strong>{{ $user->email }}</strong>.
        Pulsa el botón para crear una nueva.
    </p>

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:4px 0 8px;">
                <a href="{{ $url }}"
                   style="display:inline-block; background:#0B4258; color:#ffffff; text-decoration:none; font-weight:bold; font-size:16px; padding:15px 38px; border-radius:10px;">
                    Crear nueva contraseña
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:12px; color:#64748B;">El enlace vence en 60 minutos y solo se puede usar una vez.</td>
        </tr>
    </table>

    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:26px 0 0;">
        <tr>
            <td style="background:#FFF8EB; border-left:4px solid #F7931E; border-radius:8px; padding:12px 16px; font-size:13px; color:#7C4A03;">
                <strong>¿No fuiste tú?</strong> Ignora este correo: tu contraseña actual sigue funcionando y nadie puede cambiarla sin este enlace.
            </td>
        </tr>
    </table>

    <p style="margin:22px 0 0; font-size:12px; color:#64748B; word-break:break-all;">
        Si el botón no funciona, copia este enlace en tu navegador:<br>
        <a href="{{ $url }}" style="color:#1A6080;">{{ $url }}</a>
    </p>
@endsection
