<?php

namespace App\Application\CampanasPublicidad\Commands;

final readonly class UpdateCampanaPublicidadCommand
{
    public function __construct(
        public int     $id,
        public ?int    $programa_id             = null,
        public ?string $proposito               = null,
        public ?string $nombre                  = null,
        public ?string $plataforma               = null,
        public ?string $objetivo                = null,
        public ?string $fecha_inicio            = null,
        public ?string $fecha_fin               = null,
        public ?string $estado                  = null,
        public ?int    $leads                   = null,
        public ?float  $presupuesto_usd         = null,
        public ?float  $presupuesto_bob         = null,
        public ?string $id_campana_externa      = null,
        public ?string $responsable              = null,
        public ?string $notas                    = null,
    ) {}
}
