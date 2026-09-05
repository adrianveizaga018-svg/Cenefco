<?php

namespace App\Domain\CertConfigProgramas\Exceptions;

class CertConfigNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Configuración de certificados '{$id}' no encontrada.", 404);
    }
}
