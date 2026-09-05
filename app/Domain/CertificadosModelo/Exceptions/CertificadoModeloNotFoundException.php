<?php

namespace App\Domain\CertificadosModelo\Exceptions;

class CertificadoModeloNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Modelo de certificado '{$id}' no encontrado.", 404);
    }
}
