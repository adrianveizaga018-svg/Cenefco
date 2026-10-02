<?php

namespace App\Application\RedesSociales\Queries;

use App\Shared\Kernel\Contracts\QueryInterface;

final readonly class GetRedSocialBySlugQuery implements QueryInterface
{
    public function __construct(public string $slug) {}
}
