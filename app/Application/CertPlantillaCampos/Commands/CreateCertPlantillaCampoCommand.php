<?php

namespace App\Application\CertPlantillaCampos\Commands;

final readonly class CreateCertPlantillaCampoCommand
{
    public function __construct(
        public int     $plantilla_id,
        public string  $clave,
        public string  $etiqueta,
        public string  $tipo,
        public float   $pos_x_pct,
        public float   $pos_y_pct,
        public ?float  $ancho_pct,
        public ?float  $alto_pct,
        public ?string $fuente,
        public int     $tamano_pt,
        public string  $color,
        public string  $alineacion,
        public bool    $negrita,
        public bool    $cursiva,
        public string  $mayusculas,
        public ?string $valor_fijo,
        public bool    $activo,
        public int     $orden,
    ) {}
}
