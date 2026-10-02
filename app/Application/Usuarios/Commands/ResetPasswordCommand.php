<?php

namespace App\Application\Usuarios\Commands;

class ResetPasswordCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
        public readonly string $token,
    ) {}
}
