<?php

namespace App\Application\CertPlantillas\Commands;

final readonly class UpdateCertPlantillaCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombre        = null,
        public ?string $tipo          = null,
        public ?string $imagen_url    = null,
        public ?int    $ancho_px      = null,
        public ?int    $alto_px       = null,
        public ?string $orientacion   = null,
        public ?string $formato_salida = null,
        public ?int    $calidad_jpg   = null,
        public ?string $fuente_default = null,
        public ?string $color_default  = null,
        public ?string $estado        = null,
        public ?string $notas         = null,
    ) {}
}
