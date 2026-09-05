<?php

namespace App\Domain\SeccionesBloque\Exceptions;

class SeccionBloqueNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Sección de bloque '{$id}' no encontrada.", 404);
    }
}
