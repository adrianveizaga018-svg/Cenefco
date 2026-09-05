<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaCategoriaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Categoría de trivia '{$id}' no encontrada.", 404);
    }
}
