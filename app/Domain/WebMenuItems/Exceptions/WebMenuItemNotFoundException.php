<?php

namespace App\Domain\WebMenuItems\Exceptions;

class WebMenuItemNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Ítem de menú '{$id}' no encontrado.", 404);
    }
}
