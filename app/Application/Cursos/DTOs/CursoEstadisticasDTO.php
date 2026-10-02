<?php

namespace App\Application\Cursos\DTOs;

final readonly class CursoEstadisticasDTO
{
    public function __construct(
        public string $periodo,
        public string $fecha_inicio,
        public string $fecha_fin,
        public int $inscritos,
        public float $ingresos,
    ) {}
}
