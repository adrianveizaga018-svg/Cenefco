<?php

namespace App\Domain\PreguntasFrecuentes\Exceptions;

class PreguntaFrecuenteNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Pregunta frecuente '{$id}' no encontrada.", 404);
    }
}
