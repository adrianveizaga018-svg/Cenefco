<?php

namespace App\Domain\Ajustes\Exceptions;

use RuntimeException;

class AjusteNotFoundException extends RuntimeException
{
    public function __construct(string $key)
    {
        parent::__construct("Ajuste con clave '{$key}' no encontrado.", 404);
    }
}
