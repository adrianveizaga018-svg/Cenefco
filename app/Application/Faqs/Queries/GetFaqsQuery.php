<?php

namespace App\Application\Faqs\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetFaqsQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
