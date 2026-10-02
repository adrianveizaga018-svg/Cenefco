<?php

namespace App\Domain\CitasAsesoria\Exceptions;

class CitaAsesoriaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Cita de asesoría '{$id}' no encontrada.", 404);
    }
}
