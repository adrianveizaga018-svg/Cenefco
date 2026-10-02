<?php

namespace App\Application\CitasAsesoria\Commands;

final readonly class CreateCitaAsesoriaCommand
{
    public function __construct(
        public ?string $nombre,
        public ?string $email,
        public ?string $telefono,
        public ?string $fecha,
        public string $estado,
    ) {}
}
