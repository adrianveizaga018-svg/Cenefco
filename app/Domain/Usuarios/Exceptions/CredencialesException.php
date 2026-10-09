<?php

namespace App\Domain\Usuarios\Exceptions;

/** No se pueden enviar credenciales (sin correo, correo de otra persona, estudiante inexistente). */
class CredencialesException extends \RuntimeException
{
    public function __construct(string $mensaje, int $status = 422)
    {
        parent::__construct($mensaje, $status);
    }
}
