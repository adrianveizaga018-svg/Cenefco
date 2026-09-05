<?php

namespace App\Domain\Gastos\Exceptions;

class CategoriaGastoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Categoría de gasto '{$id}' no encontrada.", 404);
    }
}
