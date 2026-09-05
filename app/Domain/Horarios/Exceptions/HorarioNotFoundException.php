<?php

namespace App\Domain\Horarios\Exceptions;

class HorarioNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Horario '{$id}' no encontrado.", 404);
    }
}
