<?php

namespace App\Domain\RedesSociales\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class RedSocialNotFoundException extends DomainException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Red social '{$id}' no encontrada.", 404);
    }
}
