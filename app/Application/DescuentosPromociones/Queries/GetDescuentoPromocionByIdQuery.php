<?php

namespace App\Application\DescuentosPromociones\Queries;

final readonly class GetDescuentoPromocionByIdQuery
{
    public function __construct(public int $id) {}
}
