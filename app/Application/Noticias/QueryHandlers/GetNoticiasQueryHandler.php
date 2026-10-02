<?php

namespace App\Application\Noticias\QueryHandlers;

use App\Application\Noticias\Queries\GetNoticiasQuery;
use App\Domain\Noticias\Contracts\NoticiaRepositoryInterface;

class GetNoticiasQueryHandler
{
    public function __construct(private readonly NoticiaRepositoryInterface $repository) {}

    public function handle(GetNoticiasQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
