<?php

namespace App\Domain\Ayudas\Exceptions;

class AyudaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Ayuda '{$id}' no encontrada.", 404);
    }
}
