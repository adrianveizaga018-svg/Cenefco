<?php

namespace App\Domain\CategoriasNoticia\Exceptions;

use RuntimeException;

class CategoriaNoticiaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Categoría de noticia '{$id}' no encontrada.", 404);
    }
}
