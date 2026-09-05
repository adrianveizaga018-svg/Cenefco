<?php

namespace App\Domain\PaginasAcademicas\Exceptions;

class PaginaAcademicaNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Página académica '{$id}' no encontrada.", 404);
    }
}
