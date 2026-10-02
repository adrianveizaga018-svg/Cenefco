<?php

namespace App\Domain\Ventas\Exceptions;

class VentaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Venta '{$id}' no encontrada.", 404);
    }
}
