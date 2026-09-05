<?php

namespace App\Application\EnviosCertificado\DTOs;

final readonly class EnvioCertificadoDTO
{
    public function __construct(
        public int     $id,
        public int     $id_ins,
        public string  $ciudad_destino,
        public string  $fecha_envio,
        public string  $imagen_guia,
        public ?string $aclaraciones,
        public ?float  $costo,
        public ?int    $id_us_reg,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:             (int) $model->id,
            id_ins:         (int) $model->id_ins,
            ciudad_destino: $model->ciudad_destino,
            fecha_envio:    is_string($model->fecha_envio) ? substr($model->fecha_envio, 0, 10) : (string) $model->fecha_envio,
            imagen_guia:    $model->imagen_guia,
            aclaraciones:   $model->aclaraciones ?? null,
            costo:          isset($model->costo) && $model->costo !== null ? (float) $model->costo : null,
            id_us_reg:      isset($model->id_us_reg) ? (int) $model->id_us_reg : null,
            created_at:     is_string($model->created_at ?? null)
                                ? $model->created_at
                                : $model->created_at?->toIso8601String(),
        );
    }
}
