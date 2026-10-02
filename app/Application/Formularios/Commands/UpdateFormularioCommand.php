<?php

namespace App\Application\Formularios\Commands;

final readonly class UpdateFormularioCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre      = null,
        public ?string $slug        = null,
        public ?string $descripcion = null,
        public ?array  $campos      = null,
        public ?bool   $activo      = null,
    ) {}
}
