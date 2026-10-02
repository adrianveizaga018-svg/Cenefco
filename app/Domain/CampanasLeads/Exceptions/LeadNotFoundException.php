<?php

namespace App\Domain\CampanasLeads\Exceptions;

use RuntimeException;

class LeadNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Lead '{$id}' no encontrado.", 404);
    }
}
