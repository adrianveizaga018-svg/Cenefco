<?php

declare(strict_types=1);

namespace App\Domain\NotasPrensa\Exceptions;

use RuntimeException;

class NotaPrensaNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Nota de prensa '{$id}' no encontrada.", 404);
    }
}
