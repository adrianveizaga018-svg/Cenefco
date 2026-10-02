<?php

namespace App\Application\Comisiones\DTOs;

final readonly class ComisionLiquidacionDetalleDTO
{
    public function __construct(
        public int    $id_ins,
        public int    $id_pago,
        public ?int   $categoria_id,
        public ?string $categoria_nombre,
        public float  $comision_monto,
        public string $fecha_deposito,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_ins:           (int) $model->id_ins,
            id_pago:          (int) $model->id_pago,
            categoria_id:     isset($model->categoria_id) ? (int) $model->categoria_id : null,
            categoria_nombre: $model->categoria_nombre ?? null,
            comision_monto:   (float) $model->comision_monto,
            fecha_deposito:   is_string($model->fecha_deposito)
                ? substr($model->fecha_deposito, 0, 10)
                : $model->fecha_deposito->toDateString(),
        );
    }
}
