<?php

namespace App\Application\RedesSociales\Commands;

use App\Shared\Kernel\Contracts\CommandInterface;

final readonly class CreateRedSocialCommand implements CommandInterface
{
    public function __construct(
        public string  $red,
        public string  $url,
        public ?string $nombre_display  = null,
        public ?string $icono_clase     = null,
        public ?string $pixel_id        = null,
        public bool    $mostrar_footer  = true,
        public bool    $mostrar_header  = true,
        public bool    $activo          = true,
        public int     $orden           = 0,
    ) {}
}
