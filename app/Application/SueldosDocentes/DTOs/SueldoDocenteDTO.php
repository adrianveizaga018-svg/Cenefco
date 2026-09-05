<?php

namespace App\Application\SueldosDocentes\DTOs;

final readonly class SueldoDocenteDTO
{
    public function __construct(
        public int     $id,
        public int     $id_us,
        public ?int    $id_imp,
        public ?int    $id_programa,
        public string  $concepto,
        public ?string $periodo,
        public ?int    $gestion,
        public float   $monto_total,
        public ?string $observacion,
        public ?string $archivo_pdf,
        public int     $estado,
        public float   $total_pagado,
        public float   $saldo_pendiente,
        public string  $estado_pago,
        public ?string $docente_nombre,
        public ?string $docente_ci,
        public ?string $docente_celular,
        public ?string $docente_foto,
        public ?string $nombre_curso,
        public ?string $created_at,
        public array   $pagos = [],
    ) {}

    public static function fromRow(object $row, array $pagos = []): self
    {
        $montoTotal  = (float) $row->monto_total;
        $totalPagado = (float) $row->total_pagado;
        $saldo       = max(0.0, $montoTotal - $totalPagado);

        $estadoPago = match (true) {
            $montoTotal > 0 && $totalPagado >= $montoTotal => 'pagado',
            $totalPagado > 0                               => 'parcial',
            default                                        => 'pendiente',
        };

        return new self(
            id:              $row->id,
            id_us:           $row->id_us,
            id_imp:          $row->id_imp ?? null,
            id_programa:     isset($row->id_programa) ? (int) $row->id_programa : null,
            concepto:        $row->concepto,
            periodo:         $row->periodo ?? null,
            gestion:         isset($row->gestion) ? (int) $row->gestion : null,
            monto_total:     $montoTotal,
            observacion:     $row->observacion ?? null,
            archivo_pdf:     $row->archivo_pdf ?? null,
            estado:          (int) $row->estado,
            total_pagado:    $totalPagado,
            saldo_pendiente: $saldo,
            estado_pago:     $estadoPago,
            docente_nombre:  $row->docente_nombre ?? null,
            docente_ci:      $row->docente_ci ?? null,
            docente_celular: $row->docente_celular ?? null,
            docente_foto:    $row->docente_foto ?? null,
            nombre_curso:    $row->nombre_curso ?? null,
            created_at:      isset($row->created_at) ? (string) $row->created_at : null,
            pagos:           $pagos,
        );
    }
}
