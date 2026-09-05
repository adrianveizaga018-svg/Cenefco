<?php

namespace App\Domain\CertVerificaciones\Exceptions;

class CertVerificacionNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Verificación '{$id}' no encontrada.", 404);
    }
}
