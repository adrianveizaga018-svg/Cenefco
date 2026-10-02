<?php

namespace App\Domain\Autoridades\Exceptions;

use RuntimeException;

class AutoridadNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Autoridad '{$id}' no encontrada.", 404);
    }
}
