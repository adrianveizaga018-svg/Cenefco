<?php

namespace App\Domain\CartaModelos\Exceptions;

class CartaModeloNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Modelo de carta '{$id}' no encontrado.", 404);
    }
}
