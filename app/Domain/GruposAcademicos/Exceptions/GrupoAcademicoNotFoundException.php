<?php

namespace App\Domain\GruposAcademicos\Exceptions;

class GrupoAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Grupo académico '{$id}' no encontrado.", 404);
    }
}
