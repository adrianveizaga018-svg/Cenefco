<?php

namespace App\Domain\ProgramasAcademicos\Exceptions;

class ProgramaAcademicoConInscritosException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar el programa porque tiene estudiantes inscritos.', 422);
    }
}
