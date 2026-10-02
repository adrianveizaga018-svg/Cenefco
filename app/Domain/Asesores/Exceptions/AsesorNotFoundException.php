<?php

namespace App\Domain\Asesores\Exceptions;

class AsesorNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Asesor '{$id}' no encontrado.", 404);
    }
}
