<?php

namespace App\Application\Areas\Queries;

final readonly class GetAreaByIdQuery
{
    public function __construct(public int $id) {}
}
