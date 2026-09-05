<?php

declare(strict_types=1);

namespace App\Domain\Descargables\Exceptions;

use RuntimeException;

class DescargableNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Descargable '{$id}' no encontrado.", 404);
    }
}
