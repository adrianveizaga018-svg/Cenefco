<?php

namespace App\Application\Revistas\Commands;

final readonly class UpdateRevistaCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_revista,
        public ?string $descripcion_revista,
        public ?string $fecha_publicacion,
        public ?string $archivo,
        public ?int    $estado,
    ) {}
}
