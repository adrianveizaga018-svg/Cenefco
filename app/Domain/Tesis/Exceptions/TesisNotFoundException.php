<?php

namespace App\Domain\Tesis\Exceptions;

class TesisNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Tesis '{$id}' no encontrada.", 404);
    }
}
