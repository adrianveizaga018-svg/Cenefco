<?php

namespace App\Domain\UsuariosMoodle\Exceptions;

class UsuarioMoodleNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario Moodle '{$id}' no encontrado.", 404);
    }
}
