<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaPreguntaInvalidaException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('La pregunta enviada no corresponde a la pregunta actual de la partida.', 422);
    }
}
