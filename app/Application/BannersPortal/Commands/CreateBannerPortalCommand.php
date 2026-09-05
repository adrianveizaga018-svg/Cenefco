<?php

namespace App\Application\BannersPortal\Commands;

final readonly class CreateBannerPortalCommand
{
    public function __construct(
        public ?string $titulo,
        public ?string $subtitulo,
        public string  $imagen_url,
        public ?string $imagen_alt,
        public ?string $imagen_movil_url,
        public ?string $enlace_url,
        public ?string $enlace_texto,
        public ?string $enlace_target,
        public ?string $enlace_url_2,
        public ?string $enlace_texto_2,
        public ?string $posicion,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public bool    $activo,
        public int     $orden,
    ) {}
}
