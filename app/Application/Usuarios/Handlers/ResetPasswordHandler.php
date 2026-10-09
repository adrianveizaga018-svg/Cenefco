<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\ResetPasswordCommand;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordHandler
{
    public function handle(ResetPasswordCommand $command): void
    {
        // 'activacion' = enlace del correo de credenciales (vence en 7 días); por defecto, restablecimiento normal
        $broker = $command->tipo === 'activacion' ? 'activacion' : null;

        $status = Password::broker($broker)->reset(
            [
                'email' => $command->email,
                'password' => $command->password,
                'password_confirmation' => $command->passwordConfirmation,
                'token' => $command->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                    // Abrió el enlace enviado a su correo: el correo queda verificado
                    'email_verificado' => true,
                ])->save();

                // Cierra las sesiones abiertas con la contraseña anterior
                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \RuntimeException(match ($status) {
                Password::INVALID_TOKEN => 'El enlace ya fue usado o venció. Pide uno nuevo desde "¿Olvidaste tu contraseña?".',
                Password::INVALID_USER  => 'No encontramos una cuenta con ese correo.',
                default                 => __($status),
            });
        }
    }
}
