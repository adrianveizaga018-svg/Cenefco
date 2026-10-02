<?php

namespace App\Application\Gastos\DTOs;

final readonly class GastoDTO
{
    public function __construct(
        public int     $id,
        public int     $categoria_gasto_id,
        public ?string $categoria_nombre,
        public string  $concepto,
        public float   $monto,
        public string  $fecha,
        public ?string $responsable,
        public ?string $comprobante_url,
        public ?string $nota,
        public ?int    $gasto_recurrente_id,
        public ?int    $campana_publicidad_id,
        public ?string $campana_publicidad_nombre,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $m): self
    {
        return new self(
            id:                         (int) $m->id,
            categoria_gasto_id:         (int) $m->categoria_gasto_id,
            categoria_nombre:           $m->categoria->nombre ?? null,
            concepto:                   $m->concepto,
            monto:                      (float) $m->monto,
            fecha:                      (string) $m->fecha,
            responsable:                $m->responsable ?? null,
            comprobante_url:            $m->comprobante_url ?? null,
            nota:                       $m->nota ?? null,
            gasto_recurrente_id:        $m->gasto_recurrente_id ? (int) $m->gasto_recurrente_id : null,
            campana_publicidad_id:      $m->campana_publicidad_id ? (int) $m->campana_publicidad_id : null,
            campana_publicidad_nombre:  $m->campanaPublicidad->nombre ?? null,
            created_at:                 $m->created_at ? (string) $m->created_at : null,
            updated_at:                 $m->updated_at ? (string) $m->updated_at : null,
        );
    }
}
