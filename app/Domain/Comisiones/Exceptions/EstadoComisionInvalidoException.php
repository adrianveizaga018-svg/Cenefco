<?php

namespace App\Domain\Comisiones\Exceptions;

class EstadoComisionInvalidoException extends \RuntimeException
{
    public function __construct(string $estadoActual, string $accion)
    {
        parent::__construct("No se puede {$accion} una liquidación en estado '{$estadoActual}'.", 422);
    }
}
