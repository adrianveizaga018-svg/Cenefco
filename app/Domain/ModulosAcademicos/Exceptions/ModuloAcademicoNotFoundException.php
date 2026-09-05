<?php

namespace App\Domain\ModulosAcademicos\Exceptions;

class ModuloAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Módulo académico '{$id}' no encontrado.", 404);
    }
}
