<?php

namespace App\Application\ManualesInstitucionales\Commands;

final readonly class CreateManualInstitucionalCommand
{
    public function __construct(
        public string $tipo,
        public string $titulo,
        public ?string $descripcion,
        public ?string $archivo_url,
        public ?string $formato,
        public ?int $numero_paginas,
        public int $gestion,
        public int $version = 1,
        public bool $vigente = true,
        public ?string $fecha_publicacion = null,
        public ?int $publicado_por = null,
    ) {}
}
