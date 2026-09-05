<?php

namespace App\Application\Universidades\Commands;

final readonly class UpdateUniversidadCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre_universidad,
        public ?int    $id_ciudad,
        public ?int    $id_tipouniversidad,
        public ?int    $estado,
    ) {}
}
