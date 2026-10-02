<?php

namespace App\Application\Planillas\DTOs;

use App\Application\AjustesSueldo\DTOs\AjusteSueldoDTO;

final readonly class PlanillaPreviewItemDTO
{
    public function __construct(
        public int    $empleado_id,
        public string $nombre_completo,
        public string $cargo,
        public float  $monto_base,
        public float  $total_descuentos,
        public float  $total_bonos,
        public float  $monto_neto,
        public array  $ajustes,
    ) {}
}
