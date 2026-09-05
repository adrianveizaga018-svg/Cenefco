<?php

namespace App\Domain\Monografias\Exceptions;

class MonografiaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Monografía '{$id}' no encontrada.", 404);
    }
}
