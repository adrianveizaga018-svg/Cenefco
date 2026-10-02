<?php

namespace App\Domain\Planillas\Exceptions;

class PlanillaNoEliminableException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Solo se puede eliminar la planilla del mes en curso.', 422);
    }
}
