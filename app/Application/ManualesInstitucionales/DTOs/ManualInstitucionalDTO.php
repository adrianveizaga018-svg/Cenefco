<?php

namespace App\Application\ManualesInstitucionales\DTOs;

final readonly class ManualInstitucionalDTO
{
    public function __construct(
        public int $id,
        public string $tipo,
        public string $titulo,
        public ?string $descripcion,
        public ?string $archivo_url,
        public ?string $formato,
        public ?int $numero_paginas,
        public int $gestion,
        public int $version,
        public bool $vigente,
        public ?string $fecha_publicacion,
        public int $descargas,
    ) {}

    public static function fromModel(object $model): self
    {
        return new self(
            id: (int) $model->id,
            tipo: $model->tipo,
            titulo: $model->titulo,
            descripcion: $model->descripcion,
            archivo_url: $model->archivo_url,
            formato: $model->formato,
            numero_paginas: $model->numero_paginas,
            gestion: (int) $model->gestion,
            version: (int) $model->version,
            vigente: (bool) $model->vigente,
            fecha_publicacion: $model->fecha_publicacion?->toDateString(),
            descargas: (int) $model->descargas,
        );
    }
}
