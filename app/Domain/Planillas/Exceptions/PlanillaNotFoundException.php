<?php

namespace App\Domain\Planillas\Exceptions;

class PlanillaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Planilla '{$id}' no encontrada.", 404);
    }
}
