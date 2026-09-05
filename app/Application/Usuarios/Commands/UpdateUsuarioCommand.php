<?php

namespace App\Application\Usuarios\Commands;

final readonly class UpdateUsuarioCommand
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $apellido,
        public string $email,
        public ?string $password,
        public ?string $tipo,
        public ?int $rolId,
        public ?bool $activo,
    ) {}
}
