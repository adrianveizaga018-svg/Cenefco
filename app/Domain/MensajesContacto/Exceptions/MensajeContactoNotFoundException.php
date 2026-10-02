<?php

namespace App\Domain\MensajesContacto\Exceptions;

class MensajeContactoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Mensaje de contacto '{$id}' no encontrado.", 404);
    }
}
