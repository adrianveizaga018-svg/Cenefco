<?php

namespace App\Domain\HojasEvaluacion\Exceptions;

class HojaEvaluacionNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Hoja de evaluación '{$id}' no encontrada.", 404);
    }
}
