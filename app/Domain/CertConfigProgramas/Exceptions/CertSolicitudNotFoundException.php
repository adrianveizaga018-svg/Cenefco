<?php

namespace App\Domain\CertConfigProgramas\Exceptions;

class CertSolicitudNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Solicitud de certificado '{$id}' no encontrada.", 404);
    }
}
