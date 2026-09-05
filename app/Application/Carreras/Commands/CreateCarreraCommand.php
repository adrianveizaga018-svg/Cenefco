<?php

namespace App\Application\Carreras\Commands;

final readonly class CreateCarreraCommand
{
    public function __construct(
        public int $id_carrera,
        public ?int $id_us_reg,
        public ?int $num_carrera,
        public string $nombre_carrera,
        public int $estado,
    ) {}
}
