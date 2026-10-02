<?php

namespace App\Domain\Eventos\Exceptions;

use RuntimeException;

class EventoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Evento '{$id}' no encontrado.", 404);
    }
}
