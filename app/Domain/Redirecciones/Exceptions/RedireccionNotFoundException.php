<?php

namespace App\Domain\Redirecciones\Exceptions;

use RuntimeException;

class RedireccionNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Redirección '{$id}' no encontrada.", 404);
    }
}
