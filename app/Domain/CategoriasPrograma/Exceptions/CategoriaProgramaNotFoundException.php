<?php

namespace App\Domain\CategoriasPrograma\Exceptions;

use RuntimeException;

class CategoriaProgramaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Categoría de programa '{$id}' no encontrada.", 404);
    }
}
