<?php

namespace App\Application\TiposPrograma\QueryHandlers;

use App\Application\TiposPrograma\DTOs\TipoProgramaDTO;
use App\Application\TiposPrograma\Queries\GetTipoProgramaByIdQuery;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;

class GetTipoProgramaByIdQueryHandler
{
    public function __construct(private readonly TipoProgramaRepositoryInterface $repository) {}

    public function handle(GetTipoProgramaByIdQuery $query): TipoProgramaDTO
    {
        return TipoProgramaDTO::fromRow($this->repository->findById($query->id));
    }
}
