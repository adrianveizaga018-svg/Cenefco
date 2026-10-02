<?php

namespace App\Application\Aliados\Commands;

final readonly class UpdateAliadoCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre      = null,
        public ?string $logo_url    = null,
        public ?string $logo_alt    = null,
        public ?string $url_sitio   = null,
        public ?string $descripcion = null,
        public ?string $tipo        = null,
        public ?int    $orden       = null,
        public ?bool   $activo      = null,
    ) {}
}
