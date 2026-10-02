<?php

namespace App\Application\CertPlantillas\Commands;

final readonly class CreateCertPlantillaCommand
{
    public function __construct(
        public string  $nombre,
        public string  $tipo,
        public string  $imagen_url,
        public int     $ancho_px,
        public int     $alto_px,
        public string  $orientacion,
        public string  $formato_salida,
        public int     $calidad_jpg,
        public ?string $fuente_default,
        public ?string $color_default,
        public string  $estado,
        public ?string $notas,
        public int     $id_us_reg,
    ) {}
}
