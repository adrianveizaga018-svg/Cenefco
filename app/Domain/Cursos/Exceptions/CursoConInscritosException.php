<?php

namespace App\Domain\Cursos\Exceptions;

class CursoConInscritosException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar el curso porque tiene estudiantes inscritos.', 422);
    }
}
