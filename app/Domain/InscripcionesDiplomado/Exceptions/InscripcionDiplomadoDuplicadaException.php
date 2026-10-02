<?php

namespace App\Domain\InscripcionesDiplomado\Exceptions;

class InscripcionDiplomadoDuplicadaException extends \RuntimeException
{
    public function __construct(string $mensaje)
    {
        parent::__construct($mensaje, 422);
    }
}
