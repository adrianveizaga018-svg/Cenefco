<?php

namespace App\Domain\CampanasLeads\Exceptions;

use RuntimeException;

class CampanaLeadNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Campaña de leads '{$id}' no encontrada.", 404);
    }
}
