<?php

namespace App\Application\Areas\Commands;

final readonly class CreateAreaCommand
{
    public function __construct(
        public string  $titulo,
        public ?string $slug           = null,
        public ?string $descripcion    = null,
        public ?string $logo_url       = null,
        public ?string $logo_alt       = null,
        public array   $galeria        = [],
        public ?string $color          = null,
        public ?string $icono          = null,
        public int     $orden          = 0,
        public bool    $activo         = true,
        public ?string $meta_titulo    = null,
        public ?string $meta_descripcion = null,
    ) {}
}
