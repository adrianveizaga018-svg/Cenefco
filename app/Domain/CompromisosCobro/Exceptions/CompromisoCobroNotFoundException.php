<?php

namespace App\Domain\CompromisosCobro\Exceptions;

class CompromisoCobroNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Compromiso de cobro '{$id}' no encontrado.", 404);
    }
}
