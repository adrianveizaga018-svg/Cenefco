<?php

namespace App\Domain\Cursos\Exceptions;

use RuntimeException;

class CursoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Curso '{$id}' no encontrado.", 404);
    }
}
