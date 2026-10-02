<?php

namespace App\Domain\Visitas\Exceptions;

class VisitaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Visita '{$id}' no encontrada.", 404);
    }
}
