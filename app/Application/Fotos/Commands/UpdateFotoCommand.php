<?php

namespace App\Application\Fotos\Commands;

final readonly class UpdateFotoCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_foto,
        public ?string $descripcion_foto,
        public ?string $foto,
        public ?string $fecha_foto,
        public ?int    $estado,
    ) {}
}
