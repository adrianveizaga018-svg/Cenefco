<?php

namespace App\Application\Pagos\Queries;

final readonly class GetPagoByIdQuery
{
    public function __construct(public int $id) {}
}
