<?php

namespace App\Domain\Inscripciones\Exceptions;

class InscripcionDuplicadaException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('El estudiante ya tiene una inscripción activa en esta impartición.', 422);
    }
}
