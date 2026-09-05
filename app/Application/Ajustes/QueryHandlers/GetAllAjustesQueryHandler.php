<?php

namespace App\Application\Ajustes\QueryHandlers;

use App\Application\Ajustes\Queries\GetAllAjustesQuery;
use App\Domain\Ajustes\Contracts\AjusteRepositoryInterface;

class GetAllAjustesQueryHandler
{
    public function __construct(private readonly AjusteRepositoryInterface $repository) {}

    public function handle(GetAllAjustesQuery $query): array
    {
        return $this->repository->all();
    }
}
