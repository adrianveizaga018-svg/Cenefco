<?php

namespace App\Domain\Articulos\Exceptions;

class ArticuloNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Artículo '{$id}' no encontrado.", 404);
    }
}
