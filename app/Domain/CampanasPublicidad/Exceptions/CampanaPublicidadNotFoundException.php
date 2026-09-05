<?php

namespace App\Domain\CampanasPublicidad\Exceptions;

class CampanaPublicidadNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Campaña publicitaria '{$id}' no encontrada.", 404);
    }
}
