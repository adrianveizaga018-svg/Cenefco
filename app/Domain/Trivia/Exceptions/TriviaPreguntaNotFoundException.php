<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaPreguntaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Pregunta de trivia '{$id}' no encontrada.", 404);
    }
}
