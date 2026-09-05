<?php

namespace App\Domain\Organigramas\Exceptions;

use RuntimeException;

class OrganigramaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Organigrama '{$id}' no encontrado.", 404);
    }
}
