<?php

namespace App\Application\CitasAsesoria\Commands;

final readonly class UpdateCitaAsesoriaCommand
{
    public function __construct(
        public int $id,
        public ?string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $fecha,
        public ?string $estado,
    ) {}
}
