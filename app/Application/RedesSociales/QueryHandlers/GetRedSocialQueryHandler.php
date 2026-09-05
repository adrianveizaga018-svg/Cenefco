<?php

namespace App\Application\RedesSociales\QueryHandlers;

use App\Application\RedesSociales\Queries\GetRedSocialQuery;
use App\Domain\RedesSociales\Contracts\RedSocialRepositoryInterface;
use App\Shared\Kernel\Contracts\QueryHandlerInterface;
use App\Shared\Kernel\Contracts\QueryInterface;

class GetRedSocialQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly RedSocialRepositoryInterface $repository,
    ) {}

    public function handle(QueryInterface $query): array
    {
        
        return $this->repository->paginate($query->pagination);
    }
}
