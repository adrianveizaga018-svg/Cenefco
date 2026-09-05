<?php

namespace App\Application\HitosInstitucionales\Commands;

final readonly class UpdateHitoInstitucionalCommand
{
    public function __construct(
        public int     $id,
        public ?string $anio        = null,
        public ?string $titulo      = null,
        public ?string $descripcion = null,
        public ?string $imagen_url  = null,
        public ?string $imagen_alt  = null,
        public ?int    $orden       = null,
        public ?bool   $activo      = null,
    ) {}
}
