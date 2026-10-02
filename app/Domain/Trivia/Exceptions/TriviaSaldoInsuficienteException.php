<?php

namespace App\Domain\Trivia\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class TriviaSaldoInsuficienteException extends DomainException
{
    public function __construct(int $saldoDisponible, int $costoPuntos)
    {
        parent::__construct("Saldo de puntos insuficiente. Disponible: {$saldoDisponible}, requerido: {$costoPuntos}.");
    }
}
