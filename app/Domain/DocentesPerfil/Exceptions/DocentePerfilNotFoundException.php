<?php

namespace App\Domain\DocentesPerfil\Exceptions;

use RuntimeException;

class DocentePerfilNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Docente perfil '{$id}' no encontrado.", 404);
    }
}
