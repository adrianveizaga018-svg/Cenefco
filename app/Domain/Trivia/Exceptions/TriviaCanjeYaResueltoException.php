<?php

namespace App\Domain\Trivia\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class TriviaCanjeYaResueltoException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Este canje ya fue entregado o cancelado anteriormente.');
    }
}
