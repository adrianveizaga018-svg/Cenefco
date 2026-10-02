<?php

namespace App\Application\CampanasPublicidad\Commands;

final readonly class CreateCampanaPublicidadCommand
{
    public function __construct(
        public string  $nombre,
        public string  $plataforma,
        public string  $fecha_inicio,
        public ?int    $programa_id             = null,
        public string  $proposito               = 'curso',
        public ?string $objetivo                = null,
        public ?string $fecha_fin               = null,
        public string  $estado                  = 'planificada',
        public ?int    $leads                   = null,
        public ?float  $presupuesto_usd         = null,
        public ?float  $presupuesto_bob         = null,
        public ?string $id_campana_externa      = null,
        public ?string $responsable              = null,
        public ?string $notas                    = null,
    ) {}
}
