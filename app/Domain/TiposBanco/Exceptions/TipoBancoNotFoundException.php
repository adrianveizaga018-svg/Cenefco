<?php

namespace App\Domain\TiposBanco\Exceptions;

class TipoBancoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Tipo de banco '{$id}' no encontrado.", 404);
    }
}
