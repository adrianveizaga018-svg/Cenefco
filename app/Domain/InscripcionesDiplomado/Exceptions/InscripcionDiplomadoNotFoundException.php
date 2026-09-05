<?php

namespace App\Domain\InscripcionesDiplomado\Exceptions;

class InscripcionDiplomadoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Inscripción a diplomado '{$id}' no encontrada.", 404);
    }
}
