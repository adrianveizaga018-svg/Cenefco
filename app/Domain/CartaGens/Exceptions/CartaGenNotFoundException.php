<?php

namespace App\Domain\CartaGens\Exceptions;

class CartaGenNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Carta generada '{$id}' no encontrada.", 404);
    }
}
