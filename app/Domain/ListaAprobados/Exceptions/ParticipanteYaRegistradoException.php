<?php

namespace App\Domain\ListaAprobados\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class ParticipanteYaRegistradoException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Este estudiante ya está registrado como participante en esta impartición.');
    }
}
