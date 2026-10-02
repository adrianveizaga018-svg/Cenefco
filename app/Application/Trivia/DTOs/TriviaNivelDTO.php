<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaNivelDTO
{
    public function __construct(
        public int $id,
        public int $categoria_id,
        public string $nombre,
        public int $orden,
        public int $puntaje_base,
        public bool $activo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            categoria_id: $model->categoria_id,
            nombre: $model->nombre,
            orden: (int) $model->orden,
            puntaje_base: (int) $model->puntaje_base,
            activo: (bool) $model->activo,
        );
    }
}
