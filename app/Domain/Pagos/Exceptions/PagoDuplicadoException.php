<?php

namespace App\Domain\Pagos\Exceptions;

class PagoDuplicadoException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Ya existe un pago activo para esta cuota del estudiante.', 422);
    }
}
