@extends('emails.layout', [
    'titulo'    => 'Tus credenciales de acceso',
    'preheader' => 'Crea tu contraseña y entra a ver tus cursos, pagos y certificados.',
])

@section('content')
    <p style="margin:0 0 6px; font-size:13px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#1A6080;">
        Bienvenida a la plataforma
    </p>
    <h1 style="margin:0 0 14px; font-size:26px; line-height:1.25; color:#072D3A;">
        ¡Hola, {{ $user->nombre }}!
    </h1>

    @if (count($programas))
        <p style="margin:0 0 10px;">Tu inscripción quedó registrada en:</p>
        @foreach ($programas as $programa)
            <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 8px;">
                <tr>
                    <td style="background:#EAF4F8; border-left:4px solid #0B4258; border-radius:8px; padding:12px 16px; font-weight:bold; color:#0B4258;">
                        🎓 {{ $programa }}
                    </td>
                </tr>
            </table>
        @endforeach
        <p style="margin:14px 0 0;">
    @else
        <p style="margin:0;">
    @endif
        Ya tienes una cuenta en <strong>CENEFCO</strong>. Desde ella puedes ver tus cursos, revisar tus pagos y descargar tus certificados.
    </p>

    {{-- Credenciales --}}
    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0"
           style="margin:24px 0; border:1px solid #DCE6EA; border-radius:12px; overflow:hidden;">
        <tr>
            <td style="background:#F6F8F9; padding:12px 18px; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:#64748B;">
                Tus datos de acceso
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px 6px;">
                <span style="display:block; font-size:12px; color:#64748B;">Usuario</span>
                <span style="display:block; font-size:16px; font-weight:bold; color:#072D3A;">{{ $user->email }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:10px 18px 16px;">
                <span style="display:block; font-size:12px; color:#64748B;">Contraseña</span>
                <span style="display:block; font-size:15px; color:#072D3A;">La creas tú con el botón de abajo 🔒</span>
            </td>
        </tr>
    </table>

    {{-- Botón principal --}}
    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" style="padding:4px 0 8px;">
                <a href="{{ $url }}"
                   style="display:inline-block; background:#0B4258; color:#ffffff; text-decoration:none; font-weight:bold; font-size:16px; padding:15px 38px; border-radius:10px;">
                    Crear mi contraseña
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="font-size:12px; color:#64748B;">
                El botón funciona durante {{ $diasVigencia }} días.
            </td>
        </tr>
    </table>

    {{-- Pasos --}}
    <table role="presentation" class="limpia" width="100%" cellpadding="0" cellspacing="0" style="margin:28px 0 8px;">
        @foreach ([
            'Pulsa “Crear mi contraseña” y elige una contraseña segura.',
            'Inicia sesión con tu correo y tu nueva contraseña.',
            'Revisa tus cursos, pagos y certificados cuando quieras.',
        ] as $i => $paso)
            <tr>
                <td width="40" valign="top" style="padding:0 0 12px;">
                    <span style="display:inline-block; width:28px; height:28px; line-height:28px; text-align:center; border-radius:50%; background:#0B4258; color:#ffffff; font-size:13px; font-weight:bold;">{{ $i + 1 }}</span>
                </td>
                <td valign="top" style="padding:3px 0 12px; font-size:14px; color:#334155;">{{ $paso }}</td>
            </tr>
        @endforeach
    </table>

    <p style="margin:18px 0 0; padding-top:16px; border-top:1px solid #E8EEF1; font-size:12px; color:#64748B;">
        ¿El botón venció? Entra a <a href="{{ $loginUrl }}" style="color:#1A6080;">{{ $loginUrl }}</a>
        y usa “¿Olvidaste tu contraseña?” con este mismo correo.<br>
        Nunca te pediremos tu contraseña por teléfono, WhatsApp ni correo.
    </p>
@endsection
