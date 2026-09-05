<?php

namespace App\Application\Usuarios\Commands;

class RegisterUserCommand
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly string $password,
        public readonly ?int $roleId = null,
    ) {}
}
