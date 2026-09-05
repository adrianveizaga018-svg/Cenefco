<?php

declare(strict_types=1);

namespace App\Domain\Suscriptores\Exceptions;

use RuntimeException;

class SuscriptorNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Suscriptor '{$id}' no encontrado.", 404);
    }
}
