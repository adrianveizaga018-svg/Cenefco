<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo ?? config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
        .header { background: #1a56db; color: white; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .content { padding: 32px; color: #333; line-height: 1.6; }
        .btn { display: inline-block; padding: 12px 28px; background: #1a56db; color: white !important; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 16px 0; }
        .footer { background: #f9f9f9; padding: 16px; text-align: center; color: #999; font-size: 12px; border-top: 1px solid #eee; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th { background: #f0f0f0; padding: 10px; text-align: left; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="content">
            @yield('content')
        </div>
        <div class="footer">
            <p>Este email fue enviado automáticamente por {{ config('app.name') }}.</p>
            <p>Si no esperabas este correo, puedes ignorarlo.</p>
        </div>
    </div>
</body>
</html>
