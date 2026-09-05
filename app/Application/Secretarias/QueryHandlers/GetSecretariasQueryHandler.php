<?php

namespace App\Application\Secretarias\QueryHandlers;

use App\Application\Secretarias\Queries\GetSecretariasQuery;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class GetSecretariasQueryHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(GetSecretariasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
