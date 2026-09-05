<?php

namespace App\Domain\TiposEvento\Exceptions;

use RuntimeException;

class TipoEventoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Tipo de evento '{$id}' no encontrado.", 404);
    }
}
