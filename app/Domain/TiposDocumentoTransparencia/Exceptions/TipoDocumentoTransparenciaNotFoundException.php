<?php

namespace App\Domain\TiposDocumentoTransparencia\Exceptions;

use RuntimeException;

class TipoDocumentoTransparenciaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Tipo de documento de transparencia '{$id}' no encontrado.", 404);
    }
}
