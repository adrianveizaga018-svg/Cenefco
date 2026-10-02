<?php

namespace App\Application\Usuarios\Commands;

final readonly class UpdatePerfilCommand
{
    public function __construct(
        public int $userId,
        public ?string $nombre = null,
        public ?string $apellido = null,
        public ?string $ci = null,
        public ?string $telefono = null,
        public ?string $currentPassword = null,
        public ?string $newPassword = null,
    ) {}
}