<?php

namespace App\Application\Universidades\Commands;

final readonly class CreateUniversidadCommand
{
    public function __construct(
        public string $nombre_universidad,
        public ?int   $id_ciudad,
        public ?int   $id_tipouniversidad,
        public int    $estado,
        public int    $id_us_reg,
    ) {}
}
