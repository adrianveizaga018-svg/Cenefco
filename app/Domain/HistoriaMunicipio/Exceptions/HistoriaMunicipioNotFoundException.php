<?php

namespace App\Domain\HistoriaMunicipio\Exceptions;

class HistoriaMunicipioNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Entrada de historia '{$id}' no encontrada.", 404);
    }
}
