<?php

namespace App\Application\Tesis\QueryHandlers;

use App\Application\Tesis\Queries\GetTesisQuery;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;

class GetTesisQueryHandler
{
    public function __construct(private readonly TesisRepositoryInterface $repository) {}

    public function handle(GetTesisQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query, $query->tipoTesis, $query->conInactivos);
    }
}
