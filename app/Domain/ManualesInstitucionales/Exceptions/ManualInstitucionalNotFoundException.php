<?php

namespace App\Domain\ManualesInstitucionales\Exceptions;

use RuntimeException;

class ManualInstitucionalNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Manual institucional '{$id}' no encontrado.", 404);
    }
}
