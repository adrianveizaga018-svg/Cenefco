<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaPremioDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $descripcion,
        public string $tipo,
        public ?string $imagen_url,
        public int $costo_puntos,
        public ?int $stock,
        public bool $activo,
        public int $orden,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            descripcion: $model->descripcion,
            tipo: $model->tipo,
            imagen_url: $model->imagen_url,
            costo_puntos: (int) $model->costo_puntos,
            stock: $model->stock !== null ? (int) $model->stock : null,
            activo: (bool) $model->activo,
            orden: (int) $model->orden,
        );
    }
}
