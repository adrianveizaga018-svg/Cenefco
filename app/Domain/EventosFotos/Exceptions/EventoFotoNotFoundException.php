<?php

namespace App\Domain\EventosFotos\Exceptions;

use RuntimeException;

class EventoFotoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Foto de evento '{$id}' no encontrada.", 404);
    }
}
