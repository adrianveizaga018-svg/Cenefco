<?php

namespace App\Domain\Expedidos\Exceptions;

class ExpedidoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Expedido '{$id}' no encontrado.", 404);
    }
}
