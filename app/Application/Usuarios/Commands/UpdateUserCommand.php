<?php

namespace App\Application\Usuarios\Commands;

class UpdateUserCommand
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $nombre = null,
        public readonly ?string $apellido = null,
        public readonly ?string $email = null,
        public readonly ?int $roleId = null,
    ) {}
}
