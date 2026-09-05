<?php

namespace App\Domain\DescuentosPromociones\Exceptions;

use RuntimeException;

class DescuentoPromocionNotFoundException extends RuntimeException
{
    public function __construct(int|string $id)
    {
        parent::__construct("Descuento/Promoción '{$id}' no encontrado.", 404);
    }
}
