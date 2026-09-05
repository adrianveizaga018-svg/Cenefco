<?php

namespace App\Application\Gastos\Queries;

final readonly class GetGastoByIdQuery
{
    public function __construct(public int $id) {}
}
