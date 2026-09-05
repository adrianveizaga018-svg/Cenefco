<?php

namespace App\Application\HitosInstitucionales\Commands;

final readonly class CreateHitoInstitucionalCommand
{
    public function __construct(
        public string  $anio,
        public string  $titulo,
        public ?string $descripcion = null,
        public ?string $imagen_url  = null,
        public ?string $imagen_alt  = null,
        public int     $orden       = 0,
        public bool    $activo      = true,
    ) {}
}
