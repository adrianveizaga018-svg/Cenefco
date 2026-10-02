<?php

namespace App\Application\Usuarios\Commands;

final readonly class CreateUsuarioCommand
{
    public function __construct(
        public string $nombre,
        public string $apellido,
        public string $email,
        public string $password,
        public string $tipo,
        public ?int $rolId,
        public bool $activo,
    ) {}
}
