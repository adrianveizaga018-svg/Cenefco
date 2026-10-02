<?php

namespace App\Domain\Pagos\Exceptions;

class DevolucionEstadoInvalidoException extends \RuntimeException
{
    public function __construct(string $estadoActual)
    {
        parent::__construct("La devolución ya fue resuelta (estado actual: {$estadoActual}) y no puede volver a cambiar de estado.", 422);
    }
}
