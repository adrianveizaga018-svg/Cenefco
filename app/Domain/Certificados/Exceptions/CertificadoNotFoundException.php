<?php

namespace App\Domain\Certificados\Exceptions;

class CertificadoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Certificado '{$id}' no encontrado.", 404);
    }
}
