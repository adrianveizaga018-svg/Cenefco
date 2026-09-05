<?php

namespace App\Domain\Pagos\Exceptions;

class DevolucionMontoExcedeDisponibleException extends \RuntimeException
{
    public function __construct(float $montoSolicitado, float $disponible)
    {
        parent::__construct(
            "El monto solicitado (Bs. {$montoSolicitado}) excede el disponible para devolución (Bs. {$disponible}).",
            422
        );
    }
}
