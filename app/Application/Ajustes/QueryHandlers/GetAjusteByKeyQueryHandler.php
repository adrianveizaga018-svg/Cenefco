<?php

namespace App\Application\Ajustes\QueryHandlers;

use App\Application\Ajustes\DTOs\AjusteDTO;
use App\Application\Ajustes\Queries\GetAjusteByKeyQuery;
use App\Domain\Ajustes\Contracts\AjusteRepositoryInterface;

class GetAjusteByKeyQueryHandler
{
    public function __construct(private readonly AjusteRepositoryInterface $repository) {}

    public function handle(GetAjusteByKeyQuery $query): AjusteDTO
    {
        return $this->repository->findByKey($query->key);
    }
}
