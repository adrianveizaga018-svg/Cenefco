<?php

namespace App\Application\Usuarios\Commands;

class LoginGoogleCommand
{
    public function __construct(
        public readonly string $idToken,
        public readonly string $deviceName = 'web',
    ) {}
}
