<?php

namespace App\Domain\Comisiones\Exceptions;

class ComisionLiquidacionNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Liquidación de comisión '{$id}' no encontrada.", 404);
    }
}
