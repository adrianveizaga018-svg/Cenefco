<?php

namespace App\Application\Convenios\Queries;

final readonly class GetConvenioByIdQuery
{
    public function __construct(
        public int $id,
    ) {}
}
