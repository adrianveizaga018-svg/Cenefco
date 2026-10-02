<?php

namespace App\Domain\Honorarios\Contracts;

interface HonorarioSugeridoRepositoryInterface
{
    public function docentesActivosDelMesConSugerido(int $anio, int $mes): array;
}
