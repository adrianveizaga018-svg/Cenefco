<?php

namespace App\Application\Usuarios\Commands;

class LoginCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $deviceName = 'web',
    ) {}
}
