<?php

namespace App\Domain\Faqs\Exceptions;

use RuntimeException;

class FaqNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("FAQ '{$id}' no encontrada.", 404);
    }
}
