<?php

namespace App\Domain\UsuariosPrograma\Exceptions;

class UsuarioProgramaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario-Programa '{$id}' no encontrado.", 404);
    }
}
