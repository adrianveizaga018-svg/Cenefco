<?php

namespace App\Domain\ArchivosAcademicos\Exceptions;

class ArchivoAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Archivo académico '{$id}' no encontrado.", 404);
    }
}
