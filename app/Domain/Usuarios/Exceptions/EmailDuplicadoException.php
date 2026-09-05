<?php

namespace App\Domain\Usuarios\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class EmailDuplicadoException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("El email '{$email}' ya está registrado.");
    }
}
