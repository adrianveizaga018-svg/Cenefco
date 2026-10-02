<?php

namespace App\Domain\Intents\Exceptions;

class IntentNotFoundException extends \RuntimeException
{
    public function __construct(int $id)
    {
        parent::__construct("Intent '{$id}' no encontrado.", 404);
    }
}
