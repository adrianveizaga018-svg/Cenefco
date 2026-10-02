<?php

namespace App\Domain\Pagos\Exceptions;

class DevolucionNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Devolución '{$id}' no encontrada.", 404);
    }
}
