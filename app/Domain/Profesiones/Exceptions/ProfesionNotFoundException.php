<?php

namespace App\Domain\Profesiones\Exceptions;

class ProfesionNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Profesión '{$id}' no encontrada.", 404);
    }
}
