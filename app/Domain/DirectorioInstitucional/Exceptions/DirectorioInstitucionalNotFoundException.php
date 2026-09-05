<?php

namespace App\Domain\DirectorioInstitucional\Exceptions;

class DirectorioInstitucionalNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Entrada del directorio '{$id}' no encontrada.", 404);
    }
}
