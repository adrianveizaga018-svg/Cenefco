<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaDueloNotFoundException extends RuntimeException
{
    public function __construct(int|string $codigo)
    {
        parent::__construct("No se encontró ninguna sala de duelo con el código '{$codigo}'.", 404);
    }
}
