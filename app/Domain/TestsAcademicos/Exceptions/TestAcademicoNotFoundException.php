<?php

namespace App\Domain\TestsAcademicos\Exceptions;

class TestAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Test académico '{$id}' no encontrado.", 404);
    }
}
