<?php

namespace App\Domain\Pagos\Exceptions;

class PagoBoletaDuplicadaException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Ese comprobante (N° de boleta/referencia) ya está registrado en otro pago.', 422);
    }
}
