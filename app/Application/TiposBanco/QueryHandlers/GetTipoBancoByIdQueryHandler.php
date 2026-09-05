<?php

namespace App\Application\TiposBanco\QueryHandlers;

use App\Application\TiposBanco\DTOs\TipoBancoDTO;
use App\Application\TiposBanco\Queries\GetTipoBancoByIdQuery;
use App\Domain\TiposBanco\Contracts\TipoBancoRepositoryInterface;

class GetTipoBancoByIdQueryHandler
{
    public function __construct(private readonly TipoBancoRepositoryInterface $repository) {}

    public function handle(GetTipoBancoByIdQuery $query): TipoBancoDTO
    {
        return TipoBancoDTO::fromModel($this->repository->findById($query->id));
    }
}
