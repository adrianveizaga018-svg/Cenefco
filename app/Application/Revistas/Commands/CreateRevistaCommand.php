<?php

namespace App\Application\Revistas\Commands;

final readonly class CreateRevistaCommand
{
    public function __construct(
        public int     $id_revista,
        public int     $id_us_reg,
        public int     $num_revista,
        public string  $titulo_revista,
        public ?string $descripcion_revista,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public int     $estado,
    ) {}
}
