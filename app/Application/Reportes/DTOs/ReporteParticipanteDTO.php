<?php

namespace App\Application\Reportes\DTOs;

final readonly class ReporteParticipanteDTO
{
    public function __construct(
        public int     $id_ins,
        public int     $id_us,
        public int     $id_imp,
        public string  $estudiante_nombre,
        public ?string $estudiante_ci,
        public ?string $estudiante_email,
        public ?string $estudiante_celular,
        public ?string $curso_nombre,
        public ?string $periodo,
        public ?string $gestion,
        public int     $total_cuotas,
        public int     $cuotas_pagadas,
        public int     $cuotas_vencidas,
        public float   $total_plan,
        public float   $total_pagado,
        public float   $total_anticipos,
        public ?float  $pendiente,
        public bool    $plan_no_asignado,
        public string  $estado_general,
        public array   $cuotas,
        public array   $pagos,
    ) {}
}
