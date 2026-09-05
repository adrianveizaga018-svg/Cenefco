<?php

namespace App\Domain\Vendedores\Exceptions;

use RuntimeException;

class VendedorNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Vendedor '{$id}' no encontrado.", 404);
    }
}
