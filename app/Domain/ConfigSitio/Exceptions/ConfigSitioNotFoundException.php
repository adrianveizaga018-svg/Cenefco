<?php

namespace App\Domain\ConfigSitio\Exceptions;

use RuntimeException;

class ConfigSitioNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Configuración del sitio no encontrada.', 404);
    }
}
