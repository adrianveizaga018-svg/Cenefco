<?php

namespace App\Domain\FormulariosAcademicos\Exceptions;

class FormularioAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Formulario académico '{$id}' no encontrado.", 404);
    }
}
