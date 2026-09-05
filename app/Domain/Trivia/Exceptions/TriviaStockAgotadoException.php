<?php

namespace App\Domain\Trivia\Exceptions;

use App\Shared\Kernel\Exceptions\DomainException;

class TriviaStockAgotadoException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Este premio ya no tiene stock disponible.');
    }
}
