<?php

namespace App\Domain\UsuariosTipoPrograma\Exceptions;

class UsuarioTipoProgramaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Usuario-TipoPrograma '{$id}' no encontrado.", 404);
    }
}
