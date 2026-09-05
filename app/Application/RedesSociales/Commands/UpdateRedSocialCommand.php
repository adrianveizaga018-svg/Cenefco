<?php

namespace App\Application\RedesSociales\Commands;

use App\Shared\Kernel\Contracts\CommandInterface;

final readonly class UpdateRedSocialCommand implements CommandInterface
{
    public function __construct(
        public int     $id,
        public ?string $red            = null,
        public ?string $url            = null,
        public ?string $nombre_display = null,
        public ?string $icono_clase    = null,
        public ?string $pixel_id       = null,
        public ?bool   $mostrar_footer = null,
        public ?bool   $mostrar_header = null,
        public ?bool   $activo         = null,
        public ?int    $orden          = null,
    ) {}
}
