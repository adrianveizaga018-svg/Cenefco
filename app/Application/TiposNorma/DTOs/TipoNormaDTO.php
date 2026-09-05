<?php

namespace App\Application\TiposNorma\DTOs;

final readonly class TipoNormaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $sigla,
        public ?string $descripcion,
        public bool $activo,
        public ?string $slug,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            nombre: $model->nombre,
            sigla: $model->sigla,
            descripcion: $model->descripcion,
            activo: (bool) $model->activo,
            slug: $model->slug,
        );
    }
}
