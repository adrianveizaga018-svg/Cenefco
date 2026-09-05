<?php

namespace App\Application\CifrasInstitucionales\Commands;

final readonly class UpdateCifraInstitucionalCommand
{
    public function __construct(
        public int     $id,
        public ?string $valor       = null,
        public ?string $etiqueta    = null,
        public ?string $descripcion = null,
        public ?string $icono       = null,
        public ?string $color       = null,
        public ?int    $orden       = null,
        public ?bool   $activo      = null,
    ) {}
}
