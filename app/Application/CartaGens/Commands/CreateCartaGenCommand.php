<?php

namespace App\Application\CartaGens\Commands;

final readonly class CreateCartaGenCommand
{
    public function __construct(
        public int     $id_cartagen,
        public int     $id_us_reg,
        public int     $num_carta,
        public ?int    $id_us,
        public ?int    $id_cartamod,
        public ?string $textocarta,
        public ?string $textocarta1,
        public ?string $textocarta3,
        public ?int    $usar_encabezado_pie_estandar,
        public ?int    $cp_nro_contrato,
        public ?string $cp_gestion_contrato,
        public int     $estado,
    ) {}
}
