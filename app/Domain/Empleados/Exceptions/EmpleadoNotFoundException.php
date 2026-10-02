<?php

namespace App\Domain\Empleados\Exceptions;

class EmpleadoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Empleado '{$id}' no encontrado.", 404);
    }
}
