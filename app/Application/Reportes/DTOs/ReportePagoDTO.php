<?php

namespace App\Application\Reportes\DTOs;

final readonly class ReportePagoDTO
{
    public function __construct(
        public int     $id_pago,
        public ?string $fecha_deposito,
        public float   $monto_pagado,
        public ?string $metodo_pago,
        public ?string $nro_boleta_bancaria,
        public ?string $estado_verificacion,
        public bool    $es_anticipo,
        public ?string $cuota_nro,
    ) {}

    public static function fromRow(object $row, ?string $cuotaNro): self
    {
        return new self(
            id_pago:              (int) $row->id_pago,
            fecha_deposito:       $row->fecha_deposito ?? null,
            monto_pagado:         (float) $row->monto_pagado,
            metodo_pago:          $row->metodo_pago ?? null,
            nro_boleta_bancaria:  $row->nro_boleta_bancaria ?? null,
            estado_verificacion:  $row->estado_verificacion ?? null,
            es_anticipo:          (int) ($row->pago_extra ?? 0) === 1,
            cuota_nro:            $cuotaNro,
        );
    }
}
