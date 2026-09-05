<?php

namespace App\Application\Etiquetas\DTOs;

final readonly class EtiquetaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public string $slug,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            slug: $model->slug,
        );
    }
}
