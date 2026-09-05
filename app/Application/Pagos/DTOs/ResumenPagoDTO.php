<?php

namespace App\Application\Pagos\DTOs;

final readonly class ResumenPagoDTO
{
    public function __construct(
        public bool   $plan_no_asignado,
        public float  $total_pagado,
        public float  $total_anticipos,
        public int    $cuotas_pagadas,
        public int    $cuotas_totales,
        public float  $total_plan,
        public ?float $pendiente,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            plan_no_asignado: (bool)          ($data['plan_no_asignado'] ?? false),
            total_pagado:     (float)          ($data['total_pagado']     ?? 0.0),
            total_anticipos:  (float)          ($data['total_anticipos']  ?? 0.0),
            cuotas_pagadas:   (int)            ($data['cuotas_pagadas']   ?? 0),
            cuotas_totales:   (int)            ($data['cuotas_totales']   ?? 0),
            total_plan:       (float)          ($data['total_plan']       ?? 0.0),
            pendiente:        isset($data['pendiente']) ? (float) $data['pendiente'] : null,
        );
    }
}
