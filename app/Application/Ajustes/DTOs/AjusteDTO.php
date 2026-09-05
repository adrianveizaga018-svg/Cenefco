<?php

namespace App\Application\Ajustes\DTOs;

final readonly class AjusteDTO
{
    public function __construct(
        public string $clave,
        public ?string $valor,
        public string $tipo,
        public ?string $descripcion,
        public bool $editable,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            clave: $model->clave,
            valor: $model->valor,
            tipo: $model->tipo,
            descripcion: $model->descripcion,
            editable: (bool) $model->editable,
        );
    }
}
