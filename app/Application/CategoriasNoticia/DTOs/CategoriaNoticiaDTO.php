<?php

namespace App\Application\CategoriasNoticia\DTOs;

final readonly class CategoriaNoticiaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
        public ?string $descripcion,
        public ?string $color_hex,
        public bool $activa,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            slug: $model->slug,
            descripcion: $model->descripcion,
            color_hex: $model->color_hex,
            activa: (bool) $model->activa,
        );
    }
}
