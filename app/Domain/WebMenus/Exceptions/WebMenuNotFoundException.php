<?php

namespace App\Domain\WebMenus\Exceptions;

class WebMenuNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Menú '{$id}' no encontrado.", 404);
    }
}
