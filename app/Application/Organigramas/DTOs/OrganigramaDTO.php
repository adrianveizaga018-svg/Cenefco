<?php

namespace App\Application\Organigramas\DTOs;

final readonly class OrganigramaDTO
{
    public function __construct(
        public int $id,
        public int $secretaria_id,
        public ?int $parent_id,
        public int $nivel,
        public int $orden,
        public ?string $imagen_url,
        public ?string $fecha_actualizacion,
        public bool $vigente,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            secretaria_id: (int) $model->secretaria_id,
            parent_id: $model->parent_id ? (int) $model->parent_id : null,
            nivel: (int) $model->nivel,
            orden: (int) $model->orden,
            imagen_url: $model->imagen_url,
            fecha_actualizacion: $model->fecha_actualizacion?->toDateString(),
            vigente: (bool) $model->vigente,
        );
    }
}
