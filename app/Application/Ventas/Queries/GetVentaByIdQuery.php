<?php

namespace App\Application\Ventas\Queries;

final readonly class GetVentaByIdQuery
{
    public function __construct(public int $id) {}
}
