<?php

namespace App\Domain\Honorarios\Exceptions;

class ConfigHonorarioNotFoundException extends \RuntimeException
{
    public function __construct(int $idPrograma)
    {
        parent::__construct("No hay configuración de honorarios para el programa '{$idPrograma}'.", 404);
    }
}
