<?php

namespace App\Application\Cursos\DTOs;

final readonly class CursoEstadisticasDetalleDTO
{
    public function __construct(
        public string $periodo,
        public string $fecha_inicio,
        public string $fecha_fin,
        public int $total_inscritos,
        public float $total_ingresos,
        public array $inscritos,
        public array $pagos,
    ) {}
}
