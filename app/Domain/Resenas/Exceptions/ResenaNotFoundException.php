<?php

namespace App\Domain\Resenas\Exceptions;

class ResenaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Reseña '{$id}' no encontrada.", 404);
    }
}
