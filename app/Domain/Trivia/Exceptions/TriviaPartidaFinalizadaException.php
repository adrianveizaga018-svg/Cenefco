<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaPartidaFinalizadaException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Esta partida ya ha finalizado.', 422);
    }
}
