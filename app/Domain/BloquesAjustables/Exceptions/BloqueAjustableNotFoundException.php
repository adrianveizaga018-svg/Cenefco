<?php

namespace App\Domain\BloquesAjustables\Exceptions;

class BloqueAjustableNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Bloque ajustable '{$id}' no encontrado.", 404);
    }
}
