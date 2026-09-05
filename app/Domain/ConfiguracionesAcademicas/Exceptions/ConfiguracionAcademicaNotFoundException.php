<?php

namespace App\Domain\ConfiguracionesAcademicas\Exceptions;

class ConfiguracionAcademicaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Configuración académica '{$id}' no encontrada.", 404);
    }
}
