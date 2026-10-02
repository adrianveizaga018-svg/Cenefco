<?php

namespace App\Domain\MediosPago\Exceptions;

class MedioPagoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Medio de pago '{$id}' no encontrado.", 404);
    }
}
