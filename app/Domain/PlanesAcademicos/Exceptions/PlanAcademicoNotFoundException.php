<?php

namespace App\Domain\PlanesAcademicos\Exceptions;

class PlanAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Plan académico '{$id}' no encontrado.", 404);
    }
}
