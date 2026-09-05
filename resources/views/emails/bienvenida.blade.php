@extends('emails.layout')

@section('content')
    <h2>Bienvenido, {{ $user->name }}!</h2>
    <p>Tu cuenta ha sido creada exitosamente en <strong>{{ config('app.name') }}</strong>.</p>

    <table>
        <tr>
            <th>Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th>Contraseña temporal</th>
            <td><code>{{ $passwordTemporal }}</code></td>
        </tr>
        <tr>
            <th>Rol asignado</th>
            <td>{{ $user->role?->nombre ?? 'Sin rol' }}</td>
        </tr>
    </table>

    <p style="color: #e53e3e; font-weight: bold;">
        Por seguridad, cambia tu contraseña al iniciar sesión por primera vez.
    </p>

    <a href="{{ config('app.frontend_url') }}/auth/login" class="btn">
        Iniciar sesión →
    </a>

    <p style="color: #999; font-size: 12px; margin-top: 24px;">
        Si no solicitaste esta cuenta, contacta al administrador.
    </p>
@endsection
