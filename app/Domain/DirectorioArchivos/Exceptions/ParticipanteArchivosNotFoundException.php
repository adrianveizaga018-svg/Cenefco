<?php

namespace App\Domain\DirectorioArchivos\Exceptions;

use RuntimeException;

class ParticipanteArchivosNotFoundException extends RuntimeException
{
    public function __construct(int $idIns)
    {
        parent::__construct("Inscripción '{$idIns}' no encontrada.", 404);
    }
}
