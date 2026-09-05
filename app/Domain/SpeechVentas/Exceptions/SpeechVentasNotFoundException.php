<?php

namespace App\Domain\SpeechVentas\Exceptions;

class SpeechVentasNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Speech '{$id}' no encontrado.", 404);
    }
}
