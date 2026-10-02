<?php

namespace App\Application\RedesSociales\QueryHandlers;

use App\Application\RedesSociales\DTOs\RedSocialDTO;
use App\Application\RedesSociales\Queries\GetRedSocialBySlugQuery;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;
use App\Shared\Kernel\Contracts\QueryHandlerInterface;
use App\Shared\Kernel\Contracts\QueryInterface;

class GetRedSocialBySlugQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly RedSocialRepositoryInterface $repository,
    ) {}

    public function handle(QueryInterface $query): RedSocialDTO
    {
        
        return $this->repository->findBySlug($query->slug);
    }
}
