<?php

namespace App\Application\RevistasCientificas\QueryHandlers;

use App\Application\RevistasCientificas\Queries\GetRevistasCientificasQuery;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;

class GetRevistasCientificasQueryHandler
{
    public function __construct(private readonly RevistaCientificaRepositoryInterface $repository) {}

    public function handle(GetRevistasCientificasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->conInactivos);
    }
}
