<?php

namespace App\Domain\FormulariosIns\Exceptions;

class FormularioInsNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Formulario de inscripción '{$id}' no encontrado.", 404);
    }
}
