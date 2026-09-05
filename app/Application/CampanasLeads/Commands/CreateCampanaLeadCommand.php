<?php

namespace App\Application\CampanasLeads\Commands;

final readonly class CreateCampanaLeadCommand
{
    public function __construct(
        public string  $nombre,
        public ?string $descripcion  = null,
        public string  $estado       = 'activa',
        public ?string $fecha_inicio = null,
        public ?string $fecha_fin    = null,
    ) {}
}
