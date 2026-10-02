<?php

namespace App\Domain\Historiales\Exceptions;

class HistorialNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Historial '{$id}' no encontrado.", 404);
    }
}
