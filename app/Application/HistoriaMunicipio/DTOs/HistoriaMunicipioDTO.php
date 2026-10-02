<?php

namespace App\Application\HistoriaMunicipio\DTOs;

final readonly class HistoriaMunicipioDTO
{
    public function __construct(
        public int $id,
        public string $titulo,
        public ?string $contenido,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public ?string $imagen_url,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            titulo: $model->titulo,
            contenido: $model->contenido,
            fecha_inicio: $model->fecha_inicio,
            fecha_fin: $model->fecha_fin,
            imagen_url: $model->imagen_url,
            orden: (int) $model->orden,
            activo: (bool) $model->activo,
            created_at: (is_string($model->created_at ?? null) ? $model->created_at : $model->created_at?->toIso8601String()),
        );
    }
}
