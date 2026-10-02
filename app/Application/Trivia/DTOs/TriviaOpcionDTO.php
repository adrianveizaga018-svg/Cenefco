<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaOpcionDTO
{
    public function __construct(
        public int $id,
        public string $texto,
        public bool $es_correcta,
        public int $orden,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            texto: $model->texto,
            es_correcta: (bool) $model->es_correcta,
            orden: (int) $model->orden,
        );
    }
}
