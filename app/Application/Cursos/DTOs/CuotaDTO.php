<?php

namespace App\Application\Cursos\DTOs;

final readonly class CuotaDTO
{
    public function __construct(
        public int     $id_fechapago,
        public ?string $nro_pago,
        public float   $monto_a_pagar,
        public ?string $tipo_tramite,
        public ?string $fecha_inicio,
        public ?string $fecha_fin,
        public int     $obligatorio,
    ) {}

    public static function fromRow(object $row): self
    {
        return new self(
            id_fechapago:  (int) $row->id_fechapago,
            nro_pago:      $row->nro_pago ?? null,
            monto_a_pagar: (float) ($row->monto_a_pagar ?? 0),
            tipo_tramite:  $row->tipo_tramite ?? null,
            fecha_inicio:  $row->fecha_inicio ? (string) $row->fecha_inicio : null,
            fecha_fin:     $row->fecha_fin ? (string) $row->fecha_fin : null,
            obligatorio:   (int) ($row->obligatorio ?? 1),
        );
    }
}
