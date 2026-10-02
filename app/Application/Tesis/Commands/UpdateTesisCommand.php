<?php

namespace App\Application\Tesis\Commands;

final readonly class UpdateTesisCommand
{
    public function __construct(
        public int     $id,
        public ?string $titulo_tesis,
        public ?string $descripcion_tesis,
        public ?string $fecha_publicacion,
        public ?string $autor,
        public ?int    $tipo_tesis,
        public ?string $archivo,
        public ?int    $estado,
    ) {}
}
