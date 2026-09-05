<?php

namespace App\Domain\Usuarios\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(int|string $identifier)
    {
        parent::__construct("Usuario no encontrado: {$identifier}");
    }
}
