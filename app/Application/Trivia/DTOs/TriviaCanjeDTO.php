<?php

namespace App\Application\Trivia\DTOs;

final readonly class TriviaCanjeDTO
{
    public function __construct(
        public int $id,
        public string $codigo,
        public string $estado,
        public int $costo_puntos,
        public ?string $nota,
        public ?string $fecha_resolucion,
        public string $created_at,
        public int $usuario_id,
        public string $usuario_nombre,
        public ?string $usuario_email,
        public int $premio_id,
        public string $premio_nombre,
        public string $premio_tipo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            codigo: $model->codigo,
            estado: $model->estado,
            costo_puntos: (int) $model->costo_puntos,
            nota: $model->nota,
            fecha_resolucion: $model->fecha_resolucion?->toIso8601String(),
            created_at: $model->created_at?->toIso8601String(),
            usuario_id: $model->usuario_id,
            usuario_nombre: trim(($model->usuario->nombre ?? '').' '.($model->usuario->apellido ?? '')),
            usuario_email: $model->usuario->email ?? null,
            premio_id: $model->premio_id,
            premio_nombre: $model->premio->nombre ?? '',
            premio_tipo: $model->premio->tipo ?? '',
        );
    }
}
