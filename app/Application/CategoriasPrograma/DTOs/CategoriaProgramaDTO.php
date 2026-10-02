<?php

namespace App\Application\CategoriasPrograma\DTOs;

final readonly class CategoriaProgramaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
        public ?string $descripcion,
        public ?string $imagen_url,
        public ?string $imagen_alt,
        public ?string $icono,
        public ?string $color,
        public int $orden,
        public bool $activo,
        public ?string $meta_titulo,
        public ?string $meta_descripcion,
        public ?int $tipo_programa_id,
        public float $comision_monto,
        public ?string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            slug: $model->slug,
            descripcion: $model->descripcion,
            imagen_url: $model->imagen_url,
            imagen_alt: $model->imagen_alt,
            icono: $model->icono,
            color: $model->color,
            orden: (int) $model->orden,
            activo: (bool) $model->activo,
            meta_titulo: $model->meta_titulo,
            meta_descripcion: $model->meta_descripcion,
            tipo_programa_id: $model->tipo_programa_id !== null ? (int) $model->tipo_programa_id : null,
            comision_monto: (float) $model->comision_monto,
            created_at: is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String(),
            updated_at: is_string($model->updated_at ?? null) ? $model->updated_at : $model->updated_at?->toIso8601String(),
        );
    }
}
