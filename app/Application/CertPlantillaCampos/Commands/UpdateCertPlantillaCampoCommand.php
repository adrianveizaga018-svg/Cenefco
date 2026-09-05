<?php

namespace App\Application\CertPlantillaCampos\Commands;

final readonly class UpdateCertPlantillaCampoCommand
{
    public function __construct(
        public int     $id,
        public ?string $clave       = null,
        public ?string $etiqueta    = null,
        public ?string $tipo        = null,
        public ?float  $pos_x_pct   = null,
        public ?float  $pos_y_pct   = null,
        public ?float  $ancho_pct   = null,
        public ?float  $alto_pct    = null,
        public ?string $fuente      = null,
        public ?int    $tamano_pt   = null,
        public ?string $color       = null,
        public ?string $alineacion  = null,
        public ?int    $negrita     = null,
        public ?int    $cursiva     = null,
        public ?string $mayusculas  = null,
        public ?string $valor_fijo  = null,
        public ?int    $activo      = null,
        public ?int    $orden       = null,
    ) {}
}
