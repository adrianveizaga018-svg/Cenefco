<?php

namespace App\Application\SueldosDocentes\DTOs;

final readonly class PagoSueldoDTO
{
    public function __construct(
        public int     $id,
        public int     $id_sueldo,
        public float   $monto_pagado,
        public string  $fecha_pago,
        public ?string $nro_comprobante,
        public ?string $observacion,
        public ?string $comprobante_archivo,
        public int     $estado,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:                  $model->id,
            id_sueldo:           $model->id_sueldo,
            monto_pagado:        (float) $model->monto_pagado,
            fecha_pago:          $model->fecha_pago,
            nro_comprobante:     $model->nro_comprobante ?? null,
            observacion:         $model->observacion ?? null,
            comprobante_archivo: $model->comprobante_archivo ?? null,
            estado:              (int) $model->estado,
            created_at:          isset($model->created_at) ? (string) $model->created_at : null,
        );
    }
}
