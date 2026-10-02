<?php

namespace App\Domain\CifrasInstitucionales\Exceptions;

use RuntimeException;

class CifraInstitucionalNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Cifra institucional '{$id}' no encontrada.", 404);
    }
}
