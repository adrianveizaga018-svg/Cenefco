<?php

namespace App\Application\TiposBanco\QueryHandlers;

use App\Application\TiposBanco\Queries\GetTiposBancoQuery;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class GetTiposBancoQueryHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(GetTiposBancoQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
