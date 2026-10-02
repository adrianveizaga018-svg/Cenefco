<?php

namespace App\Application\Areas\Queries;

final readonly class GetAreaConCursosQuery
{
    public function __construct(
        public string $slug,
    ) {}
}
