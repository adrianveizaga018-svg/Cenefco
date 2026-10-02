<?php

namespace App\Domain\GradosAcademicos\Exceptions;

class GradoAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Grado académico '{$id}' no encontrado.", 404);
    }
}
