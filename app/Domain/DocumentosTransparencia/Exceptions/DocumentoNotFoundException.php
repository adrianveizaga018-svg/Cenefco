<?php

namespace App\Domain\DocumentosTransparencia\Exceptions;

use RuntimeException;

class DocumentoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Documento de transparencia '{$id}' no encontrado.", 404);
    }
}
