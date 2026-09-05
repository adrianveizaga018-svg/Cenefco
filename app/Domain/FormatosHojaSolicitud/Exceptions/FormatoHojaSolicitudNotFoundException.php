<?php

namespace App\Domain\FormatosHojaSolicitud\Exceptions;

class FormatoHojaSolicitudNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Formato de hoja de solicitud '{$id}' no encontrado.", 404);
    }
}
