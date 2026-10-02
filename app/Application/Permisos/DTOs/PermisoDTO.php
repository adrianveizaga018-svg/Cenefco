<?php

namespace App\Application\Permisos\DTOs;

final readonly class PermisoDTO
{
    public function __construct(
        public int $id,
        public string $codigo,
        public ?string $descripcion,
        public ?string $modulo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            codigo: $model->codigo,
            descripcion: $model->descripcion,
            modulo: $model->modulo,
        );
    }
}
