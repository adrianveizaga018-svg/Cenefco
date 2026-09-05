<?php

namespace App\Application\Planillas\Commands;

final readonly class GenerarPlanillaCommand
{
    public function __construct(
        public int $anio,
        public int $mes,
    ) {}
}
