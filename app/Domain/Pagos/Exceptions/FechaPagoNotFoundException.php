<?php

namespace App\Domain\Pagos\Exceptions;

class FechaPagoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Fecha de pago '{$id}' no encontrada.", 404);
    }
}
