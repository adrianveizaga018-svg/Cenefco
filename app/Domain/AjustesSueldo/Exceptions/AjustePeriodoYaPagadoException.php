<?php

namespace App\Domain\AjustesSueldo\Exceptions;

class AjustePeriodoYaPagadoException extends \RuntimeException
{
    public function __construct(int $anio, int $mes)
    {
        parent::__construct(
            "Ya se generó la planilla de {$mes}/{$anio}. Este ajuste no se aplicaría a ningún pago — " .
            "elimina y regenera esa planilla, o registra el ajuste para el siguiente mes.",
            422
        );
    }
}
