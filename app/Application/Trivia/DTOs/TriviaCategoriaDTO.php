<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaCategoriaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
        public ?string $descripcion,
        public ?string $imagen_url,
        public ?string $color,
        public ?int $curso_id,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            slug: $model->slug,
            descripcion: $model->descripcion,
            imagen_url: $model->imagen_url,
            color: $model->color,
            curso_id: $model->curso_id,
            orden: (int) $model->orden,
            activo: (bool) $model->activo,
            created_at: $model->created_at?->toIso8601String(),
        );
    }
}
