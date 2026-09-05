<?php

namespace App\Domain\BloquesPlantilla\Exceptions;

class BloquePlantillaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Bloque plantilla '{$id}' no encontrado.", 404);
    }
}
