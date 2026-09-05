<?php

namespace App\Domain\Universidades\Exceptions;

class UniversidadNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Universidad '{$id}' no encontrada.", 404);
    }
}
