<?php

namespace App\Domain\Normas\Exceptions;

use RuntimeException;

class NormaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Norma '{$id}' no encontrada.", 404);
    }
}
