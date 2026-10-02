<?php

namespace App\Application\Aliados\Commands;

final readonly class CreateAliadoCommand
{
    public function __construct(
        public string  $nombre,
        public string  $logo_url,
        public ?string $logo_alt    = null,
        public ?string $url_sitio   = null,
        public ?string $descripcion = null,
        public ?string $tipo        = null,
        public int     $orden       = 0,
        public bool    $activo      = true,
    ) {}
}
