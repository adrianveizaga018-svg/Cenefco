<?php

namespace App\Domain\Notas\Exceptions;

class NotaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Nota '{$id}' no encontrada.", 404);
    }
}
