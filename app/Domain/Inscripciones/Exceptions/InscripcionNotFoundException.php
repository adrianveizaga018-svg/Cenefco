<?php

namespace App\Domain\Inscripciones\Exceptions;

use RuntimeException;

class InscripcionNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Inscripción '{$id}' no encontrada.", 404);
    }
}
