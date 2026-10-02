<?php

namespace App\Domain\CertPlantillaCampos\Exceptions;

class CertPlantillaCampoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Campo de plantilla '{$id}' no encontrado.", 404);
    }
}
