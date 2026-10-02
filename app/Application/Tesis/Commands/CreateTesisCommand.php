<?php

namespace App\Application\Tesis\Commands;

final readonly class CreateTesisCommand
{
    public function __construct(
        public int     $id_tesis,
        public int     $id_us_reg,
        public int     $num_tesis,
        public string  $titulo_tesis,
        public ?string $descripcion_tesis,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?int    $tipo_tesis,
        public ?string $archivo,
        public int     $estado,
    ) {}
}
