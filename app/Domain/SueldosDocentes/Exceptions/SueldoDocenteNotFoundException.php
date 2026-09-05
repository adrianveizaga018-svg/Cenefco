<?php

namespace App\Domain\SueldosDocentes\Exceptions;

class SueldoDocenteNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Sueldo docente '{$id}' no encontrado.", 404);
    }
}
