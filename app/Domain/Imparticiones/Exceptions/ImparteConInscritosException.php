<?php

namespace App\Domain\Imparticiones\Exceptions;

class ImparteConInscritosException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar el grupo académico porque tiene estudiantes inscritos.', 422);
    }
}
