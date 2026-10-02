<?php

namespace App\Domain\ListaAprobados\Exceptions;

class ListaAprobadosNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Lista de aprobados '{$id}' no encontrada.", 404);
    }
}
