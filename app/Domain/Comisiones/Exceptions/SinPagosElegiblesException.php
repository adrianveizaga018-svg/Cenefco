<?php

namespace App\Domain\Comisiones\Exceptions;

class SinPagosElegiblesException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No hay pagos verificados sin liquidar para este vendedor en el rango indicado.', 422);
    }
}
