<?php

namespace App\Application\Cursos\DTOs;

final readonly class PlanCobrosCursoDTO
{
    public function __construct(
        public int $id_plan,
        public string $titulo,
        public float $costo,
        public int $nro_cuotas,
        public bool $creado,
    ) {}
}
