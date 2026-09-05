<?php

namespace App\Application\TiposBanco\QueryHandlers;

use App\Application\TiposBanco\Queries\GetTiposBancoActivosQuery;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class GetTiposBancoActivosQueryHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(GetTiposBancoActivosQuery $query): array
    {
        return $this->repository->findAllActivos();
    }
}
