<?php

namespace App\Application\RedesSociales\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;

final readonly class GetRedSocialByIdQuery implements QueryInterface
{
    public function __construct(public int $id) {}
}
