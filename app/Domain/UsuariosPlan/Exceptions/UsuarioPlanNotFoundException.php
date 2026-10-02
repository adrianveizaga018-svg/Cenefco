<?php

namespace App\Domain\UsuariosPlan\Exceptions;

class UsuarioPlanNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario-Plan '{$id}' no encontrado.", 404);
    }
}
