<?php

namespace App\Domain\Planillas\Exceptions;

class PlanillaDuplicadaException extends \RuntimeException
{
    public function __construct(int $anio, int $mes)
    {
        parent::__construct("Ya existe una planilla generada para {$mes}/{$anio}.", 422);
    }
}
