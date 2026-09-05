<?php

namespace App\Domain\Trivia\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class TriviaDueloPropioException extends DomainException
{
    public function __construct()
    {
        parent::__construct('No puedes unirte a tu propia sala de duelo.');
    }
}
