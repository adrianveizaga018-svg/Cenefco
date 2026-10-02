<?php

namespace App\Domain\Acreditaciones\Exceptions;

class AcreditacionNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Acreditación '{$id}' no encontrada.", 404);
    }
}
