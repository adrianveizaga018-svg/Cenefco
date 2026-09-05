<?php

namespace App\Application\Areas\Queries;

final readonly class GetAreaBySlugQuery
{
    public function __construct(public string $slug) {}
}
