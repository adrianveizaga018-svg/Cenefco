<?php

namespace App\Domain\CertPlantillas\Exceptions;

class CertPlantillaNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Plantilla de certificado '{$id}' no encontrada.", 404);
    }
}
