<?php

namespace App\Domain\Carreras\Exceptions;

class CarreraNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Carrera '{$id}' no encontrada.", 404);
    }
}
