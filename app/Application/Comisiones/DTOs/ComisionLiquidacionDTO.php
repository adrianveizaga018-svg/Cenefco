<?php

namespace App\Application\Comisiones\DTOs;

final readonly class ComisionLiquidacionDTO
{
    public function __construct(
        public int     $id,
        public int     $vendedor_id,
        public ?string $vendedor_nombre,
        public string  $fecha_desde,
        public string  $fecha_hasta,
        public float   $total_base,
        public ?int    $total_inscritos,
        public float   $monto_comision,
        public string  $estado,
        public ?int    $aprobado_por,
        public ?string $aprobado_at,
        public ?int    $pagado_por,
        public ?string $pagado_at,
        public ?string $comprobante_pago_url,
        public ?string $nota,
        public ?string $created_at,
        public array   $detalle = [],
    ) {}

    public static function fromModel(object $model, array $detalle = []): self
    {
        return new self(
            id:                    (int) $model->id,
            vendedor_id:           (int) $model->vendedor_id,
            vendedor_nombre:       $model->vendedor_nombre ?? null,
            fecha_desde:           self::fecha($model->fecha_desde),
            fecha_hasta:           self::fecha($model->fecha_hasta),
            total_base:            (float) $model->total_base,
            total_inscritos:       isset($model->total_inscritos) ? (int) $model->total_inscritos : null,
            monto_comision:        (float) $model->monto_comision,
            estado:                $model->estado,
            aprobado_por:          $model->aprobado_por !== null ? (int) $model->aprobado_por : null,
            aprobado_at:           $model->aprobado_at ?? null,
            pagado_por:            $model->pagado_por !== null ? (int) $model->pagado_por : null,
            pagado_at:             $model->pagado_at ?? null,
            comprobante_pago_url:  $model->comprobante_pago_url ?? null,
            nota:                  $model->nota ?? null,
            created_at:            is_string($model->created_at ?? null)
                                        ? $model->created_at
                                        : $model->created_at?->toIso8601String(),
            detalle:               $detalle,
        );
    }

    private static function fecha(mixed $valor): string
    {
        return is_string($valor) ? substr($valor, 0, 10) : $valor->toDateString();
    }
}
