<?php

namespace App\Domain\CompromisosCobro\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class CompromisoCobroYaExisteException extends DomainException
{
    public function __construct(int $idIns)
    {
        parent::__construct("La inscripción '{$idIns}' ya tiene un compromiso de cobro abierto. Reprograma el existente en vez de crear uno nuevo.");
    }
}
