<?php

namespace App\Application\Pagos\DTOs;

final readonly class DevolucionDTO
{
    public function __construct(
        public int     $id,
        public int     $id_ins,
        public int     $id_us,
        public float   $monto,
        public string  $motivo,
        public string  $estado,
        public ?string $nota_respuesta,
        public ?string $fecha_respuesta,
        public ?string $documento_url,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:              (int)   $model->id,
            id_ins:          (int)   $model->id_ins,
            id_us:           (int)   $model->id_us,
            monto:           (float) $model->monto,
            motivo:                  $model->motivo,
            estado:                  $model->estado,
            nota_respuesta:          $model->nota_respuesta ?? null,
            fecha_respuesta:         $model->fecha_respuesta ?? null,
            documento_url:           $model->documento_url ?? null,
            created_at:              is_string($model->created_at ?? null)
                                         ? $model->created_at
                                         : ($model->created_at?->toIso8601String() ?? null),
        );
    }
}
