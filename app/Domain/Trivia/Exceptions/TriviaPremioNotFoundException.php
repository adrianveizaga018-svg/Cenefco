<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaPremioNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Premio de trivia '{$id}' no encontrado o ya no está disponible.", 404);
    }
}
