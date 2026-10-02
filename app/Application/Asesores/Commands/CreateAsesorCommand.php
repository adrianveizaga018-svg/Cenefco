<?php

namespace App\Application\Asesores\Commands;

final readonly class CreateAsesorCommand
{
    public function __construct(
        public string $nombre,
        public string $telefono,
        public ?string $email,
        public ?string $especialidad,
        public bool $disponible,
        public bool $activo,
    ) {}
}
