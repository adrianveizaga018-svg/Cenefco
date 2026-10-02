<?php

namespace App\Application\CartaModelos\DTOs;

final readonly class CartaModeloDTO
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
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_cartamod:                  $row->id_cartamod,
            id_us_reg:                    (int) ($row->id_us_reg ?? 0),
            num_cartamod:                 (int) ($row->num_cartamod ?? 0),
            nombremodelo:                 $row->nombremodelo,
            textocarta:                   $row->textocarta ?? null,
            textocarta1:                  $row->textocarta1 ?? null,
            textocarta3:                  $row->textocarta3 ?? null,
            texto_carta:                  $row->texto_carta ?? null,
            usar_encabezado_pie_estandar: isset($row->usar_encabezado_pie_estandar) ? (int) $row->usar_encabezado_pie_estandar : null,
            estado:                       (int) ($row->estado ?? 1),
            fecha_reg:                    $row->fecha_reg ?? null,
        );
    }
}
