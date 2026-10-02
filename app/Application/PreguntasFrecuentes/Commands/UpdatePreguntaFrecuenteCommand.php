<?php

namespace App\Application\PreguntasFrecuentes\Commands;

final readonly class UpdatePreguntaFrecuenteCommand
{
    public function __construct(
        public int     $id,
        public ?string $pregunta   = null,
        public ?string $respuesta  = null,
        public ?string $categoria  = null,
        public ?int    $orden      = null,
        public ?bool   $activo     = null,
    ) {}
}
