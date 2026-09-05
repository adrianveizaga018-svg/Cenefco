<?php

namespace App\Application\TiposNorma\Commands;

final readonly class CreateTipoNormaCommand
{
    public function __construct(
        public string $nombre,
        public ?string $sigla = null,
        public ?string $descripcion = null,
        public bool $activo = true,
    ) {}
}
