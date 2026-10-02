<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña — {{ config('app.name') }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f4ff;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0f4ff;padding:40px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

                    {{-- HEADER --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 100%);border-radius:16px 16px 0 0;padding:40px 32px;text-align:center;">
                            {{-- Icono candado SVG --}}
                            <div style="width:64px;height:64px;background:rgba(255,255,255,0.15);border-radius:50%;margin:0 auto 20px;display:inline-flex;align-items:center;justify-content:center;">
                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIzMiIgaGVpZ2h0PSIzMiIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIj48cmVjdCB3aWR0aD0iMTgiIGhlaWdodD0iMTEiIHg9IjMiIHk9IjExIiByeD0iMiIgcnk9IjIiLz48cGF0aCBkPSJNNyAxMVY3YTUgNSAwIDAgMSAxMCAwdjQiLz48L3N2Zz4=" alt="lock" width="32" height="32" style="display:block;margin:16px auto 0;">
                            </div>
                            <h1 style="margin:0 0 6px;color:#ffffff;font-size:28px;font-weight:700;letter-spacing:-0.5px;">{{ config('app.name') }}</h1>
                            <p style="margin:0;color:#bfdbfe;font-size:14px;">Surge. Vende. Crece.</p>
                        </td>
                    </tr>

                    {{-- BODY --}}
                    <tr>
                        <td style="background:#ffffff;padding:40px 40px 32px;border-left:1px solid #e5e7eb;border-right:1px solid #e5e7eb;">

                            <h2 style="margin:0 0 8px;color:#1e3a8a;font-size:22px;font-weight:700;">¿Olvidaste tu contraseña?</h2>
                            <p style="margin:0 0 24px;color:#6b7280;font-size:15px;line-height:1.6;">
                                Hola <strong style="color:#111827;">{{ $user->name }}</strong>, recibimos una solicitud para restablecer la contraseña de tu cuenta.
                            </p>

                            {{-- Botón CTA --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:8px 0 28px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $url }}"
                                           style="display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 36px;border-radius:8px;letter-spacing:0.3px;">
                                            🔐 Restablecer contraseña
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Aviso de expiración --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#fef9c3;border:1px solid #fde68a;border-radius:8px;margin-bottom:28px;">
                                <tr>
                                    <td style="padding:14px 18px;">
                                        <p style="margin:0;color:#92400e;font-size:13px;line-height:1.5;">
                                            ⏱ <strong>Este enlace expira en 60 minutos.</strong>
                                            Si no solicitaste este cambio, puedes ignorar este correo con seguridad.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            {{-- Enlace de respaldo --}}
                            <p style="margin:0 0 6px;color:#9ca3af;font-size:12px;">Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
                            <p style="margin:0;word-break:break-all;">
                                <a href="{{ $url }}" style="color:#2563eb;font-size:12px;text-decoration:none;">{{ $url }}</a>
                            </p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background:#f9fafb;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
                            <p style="margin:0 0 4px;color:#9ca3af;font-size:12px;">
                                © {{ date('Y') }} <strong>{{ config('app.name') }}</strong> — Sistema Comercial
                            </p>
                            <p style="margin:0;color:#d1d5db;font-size:11px;">
                                Este email fue generado automáticamente, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
