<?php

namespace App\Application\CartaGens\Commands;

final readonly class UpdateCartaGenCommand
{
    public function __construct(
        public int     $id,
        public ?string $textocarta,
        public ?string $textocarta1,
        public ?string $textocarta3,
        public ?int    $usar_encabezado_pie_estandar,
        public ?int    $cp_nro_contrato,
        public ?string $cp_gestion_contrato,
        public ?int    $estado,
    ) {}
}
