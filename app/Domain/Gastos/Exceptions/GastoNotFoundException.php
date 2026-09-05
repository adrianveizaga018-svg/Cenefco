<?php

namespace App\Domain\Gastos\Exceptions;

class GastoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Gasto '{$id}' no encontrado.", 404);
    }
}
