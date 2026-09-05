<?php

namespace App\Domain\HitosInstitucionales\Exceptions;

use RuntimeException;

class HitoInstitucionalNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Hito institucional '{$id}' no encontrado.", 404);
    }
}
