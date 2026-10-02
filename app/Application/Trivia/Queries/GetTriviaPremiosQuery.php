<?php

namespace App\Application\Trivia\Queries;

use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetTriviaPremiosQuery
{
    public function __construct(public PaginationDTO $pagination) {}
}
