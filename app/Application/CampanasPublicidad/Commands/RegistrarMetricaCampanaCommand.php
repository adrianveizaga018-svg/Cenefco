<?php

namespace App\Application\CampanasPublicidad\Commands;

final readonly class RegistrarMetricaCampanaCommand
{
    public function __construct(
        public int     $campana_publicidad_id,
        public string  $fecha_corte,
        public ?int    $alcance              = null,
        public ?int    $impresiones          = null,
        public ?float  $frecuencia           = null,
        public ?int    $clics_enlace         = null,
        public ?float  $ctr                  = null,
        public ?float  $cpc                  = null,
        public ?float  $cpm                  = null,
        public ?int    $resultados           = null,
        public ?string $tipo_resultado       = null,
        public ?float  $costo_por_resultado  = null,
        public ?float  $gasto_periodo        = null,
        public string  $fuente               = 'manual',
        public ?string $notas                = null,
    ) {}
}
