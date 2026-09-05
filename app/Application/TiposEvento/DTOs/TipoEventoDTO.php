<?php

namespace App\Application\TiposEvento\DTOs;

final readonly class TipoEventoDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public ?string $color_hex,
        public bool $activo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            color_hex: $model->color_hex,
            activo: (bool) $model->activo,
        );
    }
}
