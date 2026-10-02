<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaCanjeUsuarioDTO
{
    public function __construct(
        public int $id,
        public string $codigo,
        public string $estado,
        public int $costo_puntos,
        public ?string $nota,
        public string $created_at,
        public int $premio_id,
        public string $premio_nombre,
        public string $premio_tipo,
        public ?string $premio_imagen_url,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            codigo: $model->codigo,
            estado: $model->estado,
            costo_puntos: (int) $model->costo_puntos,
            nota: $model->nota,
            created_at: $model->created_at?->toIso8601String(),
            premio_id: $model->premio_id,
            premio_nombre: $model->premio->nombre ?? '',
            premio_tipo: $model->premio->tipo ?? '',
            premio_imagen_url: $model->premio->imagen_url ?? null,
        );
    }
}
