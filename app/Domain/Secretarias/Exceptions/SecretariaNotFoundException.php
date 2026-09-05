<?php

namespace App\Domain\Secretarias\Exceptions;

use RuntimeException;

class SecretariaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Secretaría '{$id}' no encontrada.", 404);
    }
}
