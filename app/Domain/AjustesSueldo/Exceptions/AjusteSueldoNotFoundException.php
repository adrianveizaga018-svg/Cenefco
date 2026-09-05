<?php

namespace App\Domain\AjustesSueldo\Exceptions;

class AjusteSueldoNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Ajuste de sueldo '{$id}' no encontrado.", 404);
    }
}
