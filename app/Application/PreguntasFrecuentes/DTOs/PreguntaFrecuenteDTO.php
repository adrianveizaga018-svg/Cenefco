<?php

namespace App\Application\PreguntasFrecuentes\DTOs;

final readonly class PreguntaFrecuenteDTO
{
    public function __construct(
        public int $id,
        public string $pregunta,
        public string $respuesta,
        public ?string $categoria,
        public int $orden,
        public bool $activo,
        public ?string $created_at,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            pregunta: $model->pregunta,
            respuesta: $model->respuesta,
            categoria: $model->categoria,
            orden: (int) $model->orden,
            activo: (bool) $model->activo,
            created_at: isset($model->created_at) ? $model->created_at?->toIso8601String() : null,
        );
    }
}
