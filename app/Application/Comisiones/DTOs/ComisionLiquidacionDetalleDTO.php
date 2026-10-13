<?php

namespace App\Application\Comisiones\DTOs;

/**
 * Una venta comisionable. categoria_* es el tipo de producto de la tabla de comisiones.
 * Los campos de detalle (estudiante, precio, regla...) llegan cuando se calcula; en una
 * liquidación ya guardada solo están los montos.
 */
final readonly class ComisionLiquidacionDetalleDTO
{
    public function __construct(
        public int     $id_ins,
        public int     $id_pago,
        public ?int    $categoria_id,
        public ?string $categoria_nombre,
        public ?float  $comision_monto,
        public string  $fecha_deposito,
        public ?string $estado_comision = null,
        public ?string $regla = null,
        public ?float  $precio = null,
        public ?float  $pagado = null,
        public ?string $estudiante = null,
        public ?string $programa = null,
        public ?int    $vendedor_id = null,
        public ?string $vendedor_nombre = null,
        public ?string $motivo_acuerdo = null,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id_ins:           (int) $model->id_ins,
            id_pago:          (int) $model->id_pago,
            categoria_id:     isset($model->categoria_id) ? (int) $model->categoria_id : null,
            categoria_nombre: $model->categoria_nombre ?? null,
            comision_monto:   isset($model->comision_monto) ? (float) $model->comision_monto : null,
            fecha_deposito:   is_string($model->fecha_deposito)
                ? substr($model->fecha_deposito, 0, 10)
                : $model->fecha_deposito->toDateString(),
            estado_comision:  $model->estado_comision ?? null,
            regla:            $model->regla ?? null,
            precio:           isset($model->precio) ? (float) $model->precio : null,
            pagado:           isset($model->pagado) ? (float) $model->pagado : null,
            estudiante:       $model->estudiante ?? null,
            programa:         $model->programa ?? null,
            vendedor_id:      isset($model->vendedor_id) ? (int) $model->vendedor_id : null,
            vendedor_nombre:  $model->vendedor_nombre ?? null,
            motivo_acuerdo:   $model->motivo_acuerdo ?? null,
        );
    }
}
