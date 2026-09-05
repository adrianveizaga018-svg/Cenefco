<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaSinPreguntasDisponiblesException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Esta categoría no tiene preguntas disponibles para jugar.', 422);
    }
}
