<?php

namespace App\Application\CampanasPublicidad\DTOs;

final readonly class CampanaMetricaDTO
{
    public function __construct(
        public int     $id,
        public int     $campana_publicidad_id,
        public string  $fecha_corte,
        public ?int    $alcance,
        public ?int    $impresiones,
        public ?float  $frecuencia,
        public ?int    $clics_enlace,
        public ?float  $ctr,
        public ?float  $cpc,
        public ?float  $cpm,
        public ?int    $resultados,
        public ?string $tipo_resultado,
        public ?float  $costo_por_resultado,
        public ?float  $gasto_periodo,
        public string  $fuente,
        public ?string $notas,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                     (int) $m->id,
            campana_publicidad_id:  (int) $m->campana_publicidad_id,
            fecha_corte:             (string) $m->fecha_corte,
            alcance:                 isset($m->alcance) ? (int) $m->alcance : null,
            impresiones:             isset($m->impresiones) ? (int) $m->impresiones : null,
            frecuencia:              isset($m->frecuencia) ? (float) $m->frecuencia : null,
            clics_enlace:            isset($m->clics_enlace) ? (int) $m->clics_enlace : null,
            ctr:                     isset($m->ctr) ? (float) $m->ctr : null,
            cpc:                     isset($m->cpc) ? (float) $m->cpc : null,
            cpm:                     isset($m->cpm) ? (float) $m->cpm : null,
            resultados:              isset($m->resultados) ? (int) $m->resultados : null,
            tipo_resultado:          $m->tipo_resultado ?? null,
            costo_por_resultado:     isset($m->costo_por_resultado) ? (float) $m->costo_por_resultado : null,
            gasto_periodo:           isset($m->gasto_periodo) ? (float) $m->gasto_periodo : null,
            fuente:                  $m->fuente ?? 'manual',
            notas:                   $m->notas ?? null,
            created_at:              $m->created_at ? (string) $m->created_at : null,
        );
    }
}
