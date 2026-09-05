<?php

namespace App\Application\Pagos\DTOs;

final readonly class PagoDTO
{
    public function __construct(
        public int     $id_pago,
        public int     $id_us,
        public ?int    $id_fechapago,
        public float   $monto_pagado,
        public ?string $nro_boleta_bancaria,
        public ?string $fecha_deposito,
        public ?string $observacion_pago,
        public int     $pago_extra,
        public int     $estado,
        public ?string $fecha_reg,
        public ?string $estudiante_nombre,
        public ?string $estudiante_ci,
        public ?string $nro_nit,
        public ?string $nombre_nit,
        public ?string $metodo_pago,
        public ?int    $id_us_cajero,
        public ?string $comprobante_archivo,
        public string  $estado_verificacion,
        public ?string $nota_verificacion,
        public ?float  $monto_descuento,
        public ?string $motivo_descuento,
        public ?int    $tipo_banco_id,
        public ?string $tipo_banco_nombre,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_pago:             (int)   $model->id_pago,
            id_us:               (int)   $model->id_us,
            id_fechapago:        isset($model->id_fechapago) ? (int) $model->id_fechapago : null,
            monto_pagado:        (float) $model->monto_pagado,
            nro_boleta_bancaria: $model->nro_boleta_bancaria ?? null,
            fecha_deposito:      $model->fecha_deposito ?? null,
            observacion_pago:    $model->observacion_pago ?? null,
            pago_extra:          (int)   ($model->pago_extra ?? 0),
            estado:              (int)   $model->estado,
            fecha_reg:           $model->fecha_reg ?? null,
            estudiante_nombre:   $model->estudiante_nombre ?? null,
            estudiante_ci:       $model->estudiante_ci ?? null,
            nro_nit:             $model->nro_nit ?? null,
            nombre_nit:          $model->nombre_nit ?? null,
            metodo_pago:         $model->metodo_pago ?? null,
            id_us_cajero:        isset($model->id_us_cajero) ? (int) $model->id_us_cajero : null,
            comprobante_archivo: $model->comprobante_archivo ?? null,
            estado_verificacion: $model->estado_verificacion ?? 'verificado',
            nota_verificacion:   $model->nota_verificacion ?? null,
            monto_descuento:     isset($model->monto_descuento_extra) ? (float) $model->monto_descuento_extra : null,
            motivo_descuento:    $model->motivo_descuento ?? null,
            tipo_banco_id:       isset($model->tipo_banco_id) ? (int) $model->tipo_banco_id : null,
            tipo_banco_nombre:   $model->tipo_banco_nombre ?? null,
        );
    }
}
