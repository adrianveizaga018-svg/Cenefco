<?php

namespace App\Application\CartaModelos\Commands;

final readonly class UpdateCartaModeloCommand
{
    public function __construct(
        public int     $id,
        public ?string $nombremodelo,
        public ?string $textocarta,
        public ?string $textocarta1,
        public ?string $textocarta3,
        public ?string $texto_carta,
        public ?int    $usar_encabezado_pie_estandar,
        public ?int    $estado,
    ) {}
}
