<?php

namespace App\Application\CartaModelos\Commands;

final readonly class CreateCartaModeloCommand
{
    public function __construct(
        public int     $id_cartamod,
        public int     $id_us_reg,
        public int     $num_cartamod,
        public string  $nombremodelo,
        public ?string $textocarta,
        public ?string $textocarta1,
        public ?string $textocarta3,
        public ?string $texto_carta,
        public ?int    $usar_encabezado_pie_estandar,
        public int     $estado,
    ) {}
}
