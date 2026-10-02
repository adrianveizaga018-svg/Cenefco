<?php

namespace App\Domain\Etiquetas\Exceptions;

use RuntimeException;

class EtiquetaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Etiqueta '{$id}' no encontrada.", 404);
    }
}
