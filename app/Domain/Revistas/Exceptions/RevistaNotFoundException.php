<?php

namespace App\Domain\Revistas\Exceptions;

class RevistaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Revista '{$id}' no encontrada.", 404);
    }
}
