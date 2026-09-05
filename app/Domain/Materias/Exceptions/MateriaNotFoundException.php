<?php

namespace App\Domain\Materias\Exceptions;

class MateriaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Materia '{$id}' no encontrada.", 404);
    }
}
