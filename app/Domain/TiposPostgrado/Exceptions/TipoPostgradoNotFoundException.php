<?php

namespace App\Domain\TiposPostgrado\Exceptions;

class TipoPostgradoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Tipo de postgrado '{$id}' no encontrado.", 404);
    }
}
