<?php

namespace App\Domain\DocumentosAcademicos\Exceptions;

class DocumentoAcademicoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Documento académico '{$id}' no encontrado.", 404);
    }
}
