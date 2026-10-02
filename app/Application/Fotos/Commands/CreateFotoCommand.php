<?php

namespace App\Application\Fotos\Commands;

final readonly class CreateFotoCommand
{
    public function __construct(
        public int     $id_foto,
        public int     $id_us_reg,
        public int     $num_foto,
        public string  $titulo_foto,
        public ?string $descripcion_foto,
        public ?string $foto,
        public ?string $fecha_foto,
        public int     $estado,
    ) {}
}
