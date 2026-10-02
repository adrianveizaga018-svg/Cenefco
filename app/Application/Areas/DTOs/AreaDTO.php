<?php

namespace App\Application\Areas\DTOs;

final readonly class AreaDTO
{
    public function __construct(
        public int     $id,
        public string  $titulo,
        public string  $slug,
        public ?string $descripcion,
        public ?string $logo_url,
        public ?string $logo_alt,
        public array   $galeria,
        public ?string $color,
        public ?string $icono,
        public int     $orden,
        public bool    $activo,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        $galeria = $model->galeria ?? null;
        if (is_string($galeria)) {
            $galeria = json_decode($galeria, true) ?? [];
        }

        return new self(
            id:               (int)   $model->id,
            titulo:                   $model->titulo,
            slug:                     $model->slug,
            descripcion:              $model->descripcion ?? null,
            logo_url:                 $model->logo_url ?? null,
            logo_alt:                 $model->logo_alt ?? null,
            galeria:          (array) ($galeria ?? []),
            color:                    $model->color ?? null,
            icono:                    $model->icono ?? null,
            orden:            (int)   ($model->orden ?? 0),
            activo:           (bool)  ($model->activo ?? true),
            meta_titulo:              $model->meta_titulo ?? null,
            meta_descripcion:         $model->meta_descripcion ?? null,
            created_at: is_string($model->created_at ?? null)
                ? $model->created_at
                : ($model->created_at?->toIso8601String() ?? null),
            updated_at: is_string($model->updated_at ?? null)
                ? $model->updated_at
                : ($model->updated_at?->toIso8601String() ?? null),
        );
    }
}
