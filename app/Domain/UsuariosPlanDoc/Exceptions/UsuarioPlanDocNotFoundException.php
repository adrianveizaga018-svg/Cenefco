<?php

namespace App\Domain\UsuariosPlanDoc\Exceptions;

class UsuarioPlanDocNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario-PlanDoc '{$id}' no encontrado.", 404);
    }
}
