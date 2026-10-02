<?php

namespace App\Application\Boletines\Queries;

final readonly class GetBoletinBySlugQuery
{
    public function __construct(public string $slug) {}
}
