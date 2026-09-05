<?php

namespace App\Application\Pagos\DTOs;

final readonly class FechaPagoDTO
{
    public function __construct(
        public int     $id_fechapago,
        public int     $id_plan,
        public ?string $nro_pago,
        public float   $monto_a_pagar,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public int     $obligatorio,
        public int     $estado,
        public ?string $tipo_tramite,
        public ?string $plan_titulo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_fechapago: (int)    $model->id_fechapago,
            id_plan:      (int)    $model->id_plan,
            nro_pago:              $model->nro_pago ?? null,
            monto_a_pagar: (float) $model->monto_a_pagar,
            fecha_inicio:          $model->fecha_inicio ?? null,
            fecha_fin:             $model->fecha_fin ?? null,
            obligatorio:  (int)    ($model->obligatorio ?? 0),
            estado:       (int)    $model->estado,
            tipo_tramite:          $model->tipo_tramite ?? null,
            plan_titulo:           $model->plan_titulo ?? null,
        );
    }
}
