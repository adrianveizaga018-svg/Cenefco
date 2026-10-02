<?php

namespace App\Application\Imparticiones\Queries;

final readonly class GetImparteByIdQuery
{
    public function __construct(public int $id) {}
}
