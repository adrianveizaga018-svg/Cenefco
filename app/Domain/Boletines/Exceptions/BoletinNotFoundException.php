<?php

namespace App\Domain\Boletines\Exceptions;

class BoletinNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Boletín '{$id}' no encontrado.", 404);
    }
}
