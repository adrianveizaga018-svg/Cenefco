<?php

namespace App\Domain\Aliados\Exceptions;

use RuntimeException;

class AliadoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Aliado '{$id}' no encontrado.", 404);
    }
}
