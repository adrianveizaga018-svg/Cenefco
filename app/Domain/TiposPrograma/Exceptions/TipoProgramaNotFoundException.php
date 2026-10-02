<?php

namespace App\Domain\TiposPrograma\Exceptions;

class TipoProgramaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Tipo de programa '{$id}' no encontrado.", 404);
    }
}
