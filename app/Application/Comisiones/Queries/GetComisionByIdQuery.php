<?php

namespace App\Application\Comisiones\Queries;

final readonly class GetComisionByIdQuery
{
    public function __construct(public int $id) {}
}
