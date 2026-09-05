<?php

namespace App\Domain\FechasDoc\Exceptions;

class FechaDocNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Fecha de documento '{$id}' no encontrada.", 404);
    }
}
