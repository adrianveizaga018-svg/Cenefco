<?php

namespace App\Application\TiposPostgrado\QueryHandlers;

use App\Application\TiposPostgrado\DTOs\TipoPostgradoDTO;
use App\Application\TiposPostgrado\Queries\GetTipoPostgradoByIdQuery;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;

class GetTipoPostgradoByIdQueryHandler
{
    public function __construct(private readonly TipoPostgradoRepositoryInterface $repository) {}

    public function handle(GetTipoPostgradoByIdQuery $query): TipoPostgradoDTO
    {
        return TipoPostgradoDTO::fromRow($this->repository->findById($query->id));
    }
}
