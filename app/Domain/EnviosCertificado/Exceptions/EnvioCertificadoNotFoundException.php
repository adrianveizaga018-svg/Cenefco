<?php

namespace App\Domain\EnviosCertificado\Exceptions;

class EnvioCertificadoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Envío de certificado '{$id}' no encontrado.", 404);
    }
}
