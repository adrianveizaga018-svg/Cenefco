<?php

namespace App\Domain\CampanasPublicidad\Exceptions;

class CampanaPublicidadConGastosException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar la campaña porque tiene gastos registrados.', 422);
    }
}
