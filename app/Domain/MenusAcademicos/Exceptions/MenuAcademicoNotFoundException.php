<?php

namespace App\Domain\MenusAcademicos\Exceptions;

class MenuAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Menú académico '{$id}' no encontrado.", 404);
    }
}
