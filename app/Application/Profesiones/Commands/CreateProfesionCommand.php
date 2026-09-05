<?php

namespace App\Application\Profesiones\Commands;

final readonly class CreateProfesionCommand
{
    public function __construct(
        public string $nombre,
        public int    $orden,
        public bool   $activo,
    ) {}
}
