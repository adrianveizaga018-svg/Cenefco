<?php

namespace App\Domain\Roles\Exceptions;

class RoleNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Rol '{$id}' no encontrado.", 404);
    }
}
