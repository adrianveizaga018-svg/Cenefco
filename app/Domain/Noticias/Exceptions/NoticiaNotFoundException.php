<?php

namespace App\Domain\Noticias\Exceptions;

use RuntimeException;

class NoticiaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Noticia '{$id}' no encontrada.", 404);
    }
}
