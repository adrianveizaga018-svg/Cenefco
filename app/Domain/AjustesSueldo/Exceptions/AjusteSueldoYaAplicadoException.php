<?php

namespace App\Domain\AjustesSueldo\Exceptions;

class AjusteSueldoYaAplicadoException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este ajuste ya fue aplicado en una planilla y no puede eliminarse.', 422);
    }
}
