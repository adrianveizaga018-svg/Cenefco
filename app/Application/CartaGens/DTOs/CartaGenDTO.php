<?php

namespace App\Application\CartaGens\DTOs;

final readonly class CartaGenDTO
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
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_cartagen:                  $row->id_cartagen,
            id_us_reg:                    (int) ($row->id_us_reg ?? 0),
            num_carta:                    (int) ($row->num_carta ?? 0),
            id_us:                        isset($row->id_us) ? (int) $row->id_us : null,
            id_cartamod:                  isset($row->id_cartamod) ? (int) $row->id_cartamod : null,
            textocarta:                   $row->textocarta ?? null,
            textocarta1:                  $row->textocarta1 ?? null,
            textocarta3:                  $row->textocarta3 ?? null,
            usar_encabezado_pie_estandar: isset($row->usar_encabezado_pie_estandar) ? (int) $row->usar_encabezado_pie_estandar : null,
            cp_nro_contrato:              isset($row->cp_nro_contrato) ? (int) $row->cp_nro_contrato : null,
            cp_gestion_contrato:          $row->cp_gestion_contrato ?? null,
            estado:                       (int) ($row->estado ?? 1),
            fecha_reg:                    $row->fecha_reg ?? null,
        );
    }
}
