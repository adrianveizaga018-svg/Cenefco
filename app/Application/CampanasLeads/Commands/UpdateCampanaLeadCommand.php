<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class UpdateCampanaLeadCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre       = null,
        public ?string $descripcion  = null,
        public ?string $estado       = null,
        public ?string $fecha_inicio = null,
        public ?string $fecha_fin    = null,
    ) {}
}
