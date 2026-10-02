<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaPartidaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Partida de trivia '{$id}' no encontrada.", 404);
    }
}
