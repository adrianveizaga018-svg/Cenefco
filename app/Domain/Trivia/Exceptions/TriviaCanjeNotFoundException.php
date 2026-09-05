<?php

namespace App\Domain\Trivia\Exceptions;

use RuntimeException;

class TriviaCanjeNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Canje '{$id}' no encontrado.", 404);
    }
}
