<?php

namespace App\Domain\ProgramasAcademicos\Exceptions;

use RuntimeException;

class ProgramaAcademicoNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Programa académico '{$id}' no encontrado.", 404);
    }
}
