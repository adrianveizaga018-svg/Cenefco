<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\ForgotPasswordCommand;
use Illuminate\Support\Facades\Password;

class ForgotPasswordHandler
{
    public function handle(ForgotPasswordCommand $command): void
    {
        $status = Password::sendResetLink(['email' => $command->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \RuntimeException(__($status));
        }
    }
}
