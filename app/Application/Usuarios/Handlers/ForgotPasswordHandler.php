<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\ForgotPasswordCommand;
use Illuminate\Support\Facades\Password;

class ForgotPasswordHandler
{
    public function handle(ForgotPasswordCommand $command): void
    {
        $status = Password::sendResetLink(['email' => $command->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return;
        }

        throw new \RuntimeException(match ($status) {
            Password::INVALID_USER     => 'No encontramos una cuenta con ese correo electrónico.',
            // Hay un enlace enviado hace segundos (p. ej. el correo de credenciales de Caja)
            Password::RESET_THROTTLED  => 'Ya te enviamos un enlace hace un momento. Revisa tu correo, incluida la carpeta de spam, y espera un minuto antes de pedir otro.',
            default                    => __($status),
        }, $status === Password::INVALID_USER ? 404 : 429);
    }
}
