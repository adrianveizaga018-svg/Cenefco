<?php

namespace App\Application\RedesSociales\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;
use App\Shared\Kernel\DTOs\PaginationDTO;

final readonly class GetRedSocialQuery implements QueryInterface
{
    public function __construct(public PaginationDTO $pagination) {}
}
