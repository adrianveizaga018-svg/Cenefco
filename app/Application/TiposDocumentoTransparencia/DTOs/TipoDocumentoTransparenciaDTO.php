<?php

namespace App\Application\TiposDocumentoTransparencia\DTOs;

final readonly class TipoDocumentoTransparenciaDTO
{
    public function __construct(
        public int $id,
        public string $nombre,
        public bool $activo,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: $model->id,
            nombre: $model->nombre,
            activo: (bool) $model->activo,
        );
    }
}
