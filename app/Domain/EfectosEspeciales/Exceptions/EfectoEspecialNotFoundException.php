<?php

namespace App\Domain\EfectosEspeciales\Exceptions;

use RuntimeException;

class EfectoEspecialNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Efecto especial '{$id}' no encontrado.", 404);
    }
}
