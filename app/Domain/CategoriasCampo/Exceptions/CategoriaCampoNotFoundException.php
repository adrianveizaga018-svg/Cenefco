<?php

namespace App\Domain\CategoriasCampo\Exceptions;

use RuntimeException;

class CategoriaCampoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Campo '{$id}' no encontrado.", 404);
    }
}
