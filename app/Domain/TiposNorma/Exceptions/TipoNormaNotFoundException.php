<?php

namespace App\Domain\TiposNorma\Exceptions;

use RuntimeException;

class TipoNormaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Tipo de norma '{$id}' no encontrado.", 404);
    }
}
