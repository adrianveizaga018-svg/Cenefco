<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\ResetPasswordCommand;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordHandler
{
    public function handle(ResetPasswordCommand $command): void
    {
        $status = Password::reset(
            [
                'email' => $command->email,
                'password' => $command->password,
                'password_confirmation' => $command->passwordConfirmation,
                'token' => $command->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \RuntimeException(__($status));
        }
    }
}
