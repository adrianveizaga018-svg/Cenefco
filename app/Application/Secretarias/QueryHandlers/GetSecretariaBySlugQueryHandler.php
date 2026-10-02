<?php

namespace App\Application\Secretarias\QueryHandlers;

use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Application\Secretarias\Queries\GetSecretariaBySlugQuery;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class GetSecretariaBySlugQueryHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(GetSecretariaBySlugQuery $query): SecretariaDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
