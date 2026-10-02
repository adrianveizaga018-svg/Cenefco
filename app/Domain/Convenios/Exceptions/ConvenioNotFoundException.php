<?php

namespace App\Domain\Convenios\Exceptions;

use RuntimeException;

class ConvenioNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Convenio '{$id}' no encontrado.", 404);
    }
}
