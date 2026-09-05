<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaPreguntaDTO
{
    public function __construct(
        public int $id,
        public int $categoria_id,
        public int $nivel_id,
        public string $enunciado,
        public ?string $imagen_url,
        public int $tiempo_limite_segundos,
        public bool $activo,
        public ?string $created_at,
        public array $opciones = [],
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            categoria_id: $model->categoria_id,
            nivel_id: $model->nivel_id,
            enunciado: $model->enunciado,
            imagen_url: $model->imagen_url,
            tiempo_limite_segundos: (int) $model->tiempo_limite_segundos,
            activo: (bool) $model->activo,
            created_at: $model->created_at?->toIso8601String(),
            opciones: $model->relationLoaded('opciones')
                ? $model->opciones->map(fn ($o) => TriviaOpcionDTO::fromModel($o))->all()
                : [],
        );
    }
}
