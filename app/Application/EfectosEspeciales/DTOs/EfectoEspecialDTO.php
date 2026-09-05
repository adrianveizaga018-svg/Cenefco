<?php

namespace App\Application\EfectosEspeciales\DTOs;

final readonly class EfectoEspecialDTO
{
    public function __construct(
        public int     $id,
        public string  $nombre,
        public string  $tipo_efecto,
        public ?string $color_primario,
        public ?string $color_secundario,
        public string  $fecha_inicio,
        public string  $fecha_fin,
        public int     $intensidad,
        public bool    $activo,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id:               $model->id,
            nombre:           $model->nombre,
            tipo_efecto:      $model->tipo_efecto,
            color_primario:   $model->color_primario,
            color_secundario: $model->color_secundario,
            fecha_inicio:     $model->fecha_inicio,
            fecha_fin:        $model->fecha_fin,
            intensidad:       (int) $model->intensidad,
            activo:           (bool) $model->activo,
            created_at:       is_string($model->created_at ?? null)
                                  ? $model->created_at
                                  : $model->created_at?->toIso8601String(),
        );
    }
}
