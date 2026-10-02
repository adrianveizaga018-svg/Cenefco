<?php

namespace App\Domain\Catalogos\Exceptions;

use RuntimeException;

class CatalogoItemNotFoundException extends RuntimeException
{
    public function __construct(string $catalogo, int $id)
    {
        parent::__construct("Registro '{$id}' no encontrado en catálogo '{$catalogo}'.", 404);
    }
}
