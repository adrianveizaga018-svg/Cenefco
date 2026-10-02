<?php

namespace App\Application\Planillas\DTOs;

final readonly class PlanillaDTO
{
    public function __construct(
        public int     $id,
        public int     $anio,
        public int     $mes,
        public float   $total,
        public int     $gasto_id,
        public array   $detalle,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:         (int) $m->id,
            anio:       (int) $m->anio,
            mes:        (int) $m->mes,
            total:      (float) $m->total,
            gasto_id:   (int) $m->gasto_id,
            detalle:    collect($m->detalle ?? [])->map(fn ($d) => PlanillaDetalleDTO::fromModel($d))->all(),
            created_at: $m->created_at ? (string) $m->created_at : null,
        );
    }
}
