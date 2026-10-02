<?php

namespace App\Application\Asesores\Commands;

final readonly class UpdateAsesorCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre,
        public ?string $telefono,
        public ?string $email,
        public ?string $especialidad,
        public ?bool $disponible,
        public ?bool $activo,
    ) {}
}
