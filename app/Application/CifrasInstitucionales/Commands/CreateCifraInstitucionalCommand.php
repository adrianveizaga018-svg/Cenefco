<?php

namespace App\Application\CifrasInstitucionales\Commands;

final readonly class CreateCifraInstitucionalCommand
{
    public function __construct(
        public string  $valor,
        public string  $etiqueta,
        public ?string $descripcion = null,
        public ?string $icono       = null,
        public ?string $color       = null,
        public int     $orden       = 0,
        public bool    $activo      = true,
    ) {}
}
