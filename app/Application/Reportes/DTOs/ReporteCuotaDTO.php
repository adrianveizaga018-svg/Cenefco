<?php

namespace App\Application\Reportes\DTOs;

final readonly class ReporteCuotaDTO
{
    public function __construct(
        public int     $id_fechapago,
        public ?string $cuota_nro,
        public float   $cuota_monto,
        public ?string $cuota_fecha_inicio,
        public ?string $cuota_fecha_fin,
        public ?string $tipo_tramite,
        public float   $monto_pagado,
        public int     $nro_pagos,
        public ?string $ultima_fecha_pago,
        public ?string $estado_verificacion,
        public string  $estado_cuota,
        public int     $dias_atraso,
    ) {}

    public static function fromRow(object $row): self
    {
        $cuotaMonto   = (float) $row->cuota_monto;
        $montoPagado  = (float) ($row->cuota_monto_pagado ?? 0);
        $hoy          = now()->toDateString();
        $fechaFin     = $row->cuota_fecha_fin;
        $vencida      = $fechaFin && $fechaFin < $hoy;

        $estadoCuota = match (true) {
            $montoPagado >= $cuotaMonto - 0.01 && $cuotaMonto > 0 => 'pagada',
            $montoPagado > 0                                      => 'parcial',
            $vencida                                              => 'vencida',
            default                                                => 'pendiente',
        };

        $diasAtraso = ($vencida && $estadoCuota !== 'pagada')
            ? (int) now()->diffInDays($fechaFin, absolute: true)
            : 0;

        return new self(
            id_fechapago:        (int) $row->id_fechapago,
            cuota_nro:           $row->cuota_nro ?? null,
            cuota_monto:         $cuotaMonto,
            cuota_fecha_inicio:  $row->cuota_fecha_inicio ?? null,
            cuota_fecha_fin:     $fechaFin,
            tipo_tramite:        $row->tipo_tramite ?? null,
            monto_pagado:        $montoPagado,
            nro_pagos:           (int) ($row->cuota_nro_pagos ?? 0),
            ultima_fecha_pago:   $row->cuota_ultima_fecha_pago ?? null,
            estado_verificacion: $row->cuota_estado_verificacion ?? null,
            estado_cuota:        $estadoCuota,
            dias_atraso:         $diasAtraso,
        );
    }
}
