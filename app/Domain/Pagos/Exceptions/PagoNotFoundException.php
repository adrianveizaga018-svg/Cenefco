<?php

namespace App\Domain\Pagos\Exceptions;

class PagoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Pago '{$id}' no encontrado.", 404);
    }
}
