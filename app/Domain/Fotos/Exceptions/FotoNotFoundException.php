<?php

namespace App\Domain\Fotos\Exceptions;

class FotoNotFoundException extends \RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Foto '{$id}' no encontrada.", 404);
    }
}
