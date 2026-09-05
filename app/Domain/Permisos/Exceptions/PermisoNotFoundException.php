<?php

namespace App\Domain\Permisos\Exceptions;

class PermisoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Permiso '{$id}' no encontrado.", 404);
    }
}
