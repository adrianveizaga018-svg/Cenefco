<?php

namespace App\Application\Faqs\Queries;

final readonly class GetFaqByIdQuery
{
    public function __construct(public int $id) {}
}
