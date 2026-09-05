<?php

namespace App\Domain\Imparticiones\Exceptions;

use RuntimeException;

class ImparteNotFoundException extends RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Impartición '{$id}' no encontrada.", 404);
    }
}
