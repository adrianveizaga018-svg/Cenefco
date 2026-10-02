<?php

namespace App\Domain\Trivia\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class TriviaDueloYaIniciadoException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Esta sala de duelo ya está llena o en curso.');
    }
}
