<?php

namespace App\Domain\Gastos\Exceptions;

class GastoRecurrenteNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Gasto recurrente '{$id}' no encontrado.", 404);
    }
}
