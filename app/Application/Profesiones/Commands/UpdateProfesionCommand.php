<?php

namespace App\Application\Profesiones\Commands;

final readonly class UpdateProfesionCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre,
        public ?int    $orden,
        public ?bool   $activo,
    ) {}
}
