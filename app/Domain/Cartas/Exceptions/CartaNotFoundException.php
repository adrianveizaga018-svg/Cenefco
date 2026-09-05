<?php

namespace App\Domain\Cartas\Exceptions;

class CartaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Carta '{$id}' no encontrada.", 404);
    }
}
