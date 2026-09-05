<?php

namespace App\Application\Ayudas\DTOs;

final readonly class AyudaDTO
{
    public function __construct(
        public int     $id_ayuda,
        public int     $id_us_reg,
        public int     $num_ayuda,
        public int     $id_us,
        public ?string $gestion,
        public ?string $monto_pagado,
        public ?string $nro_recibo,
        public ?string $fecha_recibo,
        public ?string $observacion_pago,
        public ?int    $id_categoriatipoayuda,
        public int     $estado,
        public ?string $fecha_reg,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_ayuda:              $row->id_ayuda,
            id_us_reg:             (int) ($row->id_us_reg ?? 0),
            num_ayuda:             (int) ($row->num_ayuda ?? 0),
            id_us:                 (int) ($row->id_us ?? 0),
            gestion:               $row->gestion ?? null,
            monto_pagado:          $row->monto_pagado ?? null,
            nro_recibo:            $row->nro_recibo ?? null,
            fecha_recibo:          $row->fecha_recibo ?? null,
            observacion_pago:      $row->observacion_pago ?? null,
            id_categoriatipoayuda: isset($row->id_categoriatipoayuda) ? (int) $row->id_categoriatipoayuda : null,
            estado:                (int) ($row->estado ?? 1),
            fecha_reg:             $row->fecha_reg ?? null,
        );
    }
}
