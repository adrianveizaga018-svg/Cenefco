<?php

namespace App\Application\Cursos\Commands;

final readonly class ObtenerOCrearPlanCobrosCommand
{
    public function __construct(
        public int $idPrograma,
        public int $idUsReg,
    ) {}
}
