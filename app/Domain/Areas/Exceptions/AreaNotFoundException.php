<?php

namespace App\Domain\Areas\Exceptions;

class AreaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Área '{$id}' no encontrada.", 404);
    }
}
