<?php

namespace App\Domain\Gastos\Exceptions;

class GastoRecurrenteYaConfirmadoException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Este gasto recurrente ya fue confirmado en el mes seleccionado.', 422);
    }
}
