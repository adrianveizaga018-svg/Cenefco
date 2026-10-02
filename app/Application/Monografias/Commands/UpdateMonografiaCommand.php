<?php

namespace App\Application\Monografias\Commands;

final readonly class UpdateMonografiaCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_monografia,
        public ?string $descripcion_monografia,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?string $archivo,
        public ?int    $estado,
    ) {}
}
