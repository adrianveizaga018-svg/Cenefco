<?php

namespace App\Domain\Comunicados\Exceptions;

class ComunicadoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Comunicado '{$id}' no encontrado.", 404);
    }
}
