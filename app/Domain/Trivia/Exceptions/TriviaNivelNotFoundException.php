<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaNivelNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Nivel de trivia '{$id}' no encontrado.", 404);
    }
}
