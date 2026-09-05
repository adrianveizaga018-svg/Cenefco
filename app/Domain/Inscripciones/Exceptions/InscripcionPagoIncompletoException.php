<?php

namespace App\Domain\Inscripciones\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class InscripcionPagoIncompletoException extends DomainException
{
    public function __construct(?float $pendiente = null)
    {
        $detalle = $pendiente !== null ? sprintf(' (saldo pendiente: Bs %.2f)', $pendiente) : '';
        parent::__construct("El inscrito aún no terminó de pagar{$detalle}; no se puede marcar como participante.");
    }
}
