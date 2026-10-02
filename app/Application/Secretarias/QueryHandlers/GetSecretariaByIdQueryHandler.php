<?php

namespace App\Application\Secretarias\QueryHandlers;

use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Application\Secretarias\Queries\GetSecretariaByIdQuery;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class GetSecretariaByIdQueryHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(GetSecretariaByIdQuery $query): SecretariaDTO
    {
        return $this->repository->findById($query->id);
    }
}
