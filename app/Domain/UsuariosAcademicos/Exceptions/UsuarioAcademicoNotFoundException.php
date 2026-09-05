<?php

namespace App\Domain\UsuariosAcademicos\Exceptions;

use RuntimeException;

class UsuarioAcademicoNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario académico '{$id}' no encontrado.", 404);
    }
}
