<?php

namespace App\Domain\Ventas\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class EmailNoDisponibleException extends DomainException
{
    public function __construct()
    {
        parent::__construct('El estudiante no tiene un correo electrónico registrado. Ingrese uno manualmente.');
    }
}
