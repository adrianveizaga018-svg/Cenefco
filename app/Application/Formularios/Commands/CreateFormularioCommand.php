<?php

namespace App\Application\Formularios\Commands;

final readonly class CreateFormularioCommand
{
    public function __construct(
        public string  $nombre,
        public ?string $slug        = null,
        public ?string $descripcion = null,
        public array   $campos      = [],
        public bool    $activo      = true,
    ) {}
}
