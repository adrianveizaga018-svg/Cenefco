<?php

namespace App\Application\Reportes\DTOs;

final readonly class ReporteVentasPorPeriodoDTO
{
    public function __construct(
        public string $gestion,
        public string $periodo,
        public int    $total_inscripciones,
        public int    $total_pagos,
        public float  $total_a_pagar,
        public float  $total_pagado,
        public float  $saldo_pendiente,
        public int    $pagados,
        public int    $parciales,
        public int    $pendientes,
    ) {}

    public static function fromRow(object $row): self
    {
        $totalAPagar  = (float) $row->total_a_pagar;
        $totalPagado  = (float) $row->total_pagado;

        return new self(
            gestion:              $row->gestion,
            periodo:              $row->periodo,
            total_inscripciones:  (int) $row->total_inscripciones,
            total_pagos:          (int) $row->total_pagos,
            total_a_pagar:        $totalAPagar,
            total_pagado:         $totalPagado,
            saldo_pendiente:      max(0.0, $totalAPagar - $totalPagado),
            pagados:              (int) $row->pagados,
            parciales:            (int) $row->parciales,
            pendientes:           (int) $row->pendientes,
        );
    }
}
