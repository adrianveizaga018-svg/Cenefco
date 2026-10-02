<?php

namespace App\Domain\SugerenciasReclamos\Exceptions;

use RuntimeException;

class SugerenciaReclamoNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Sugerencia/Reclamo '{$id}' no encontrado.", 404);
    }
}
