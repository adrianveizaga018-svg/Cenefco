<?php

namespace App\Domain\Testimonios\Exceptions;

use RuntimeException;

class TestimonioNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Testimonio '{$id}' no encontrado.", 404);
    }
}
