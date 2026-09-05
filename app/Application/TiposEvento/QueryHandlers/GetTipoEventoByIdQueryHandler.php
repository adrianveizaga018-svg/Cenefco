<?php

namespace App\Application\TiposEvento\QueryHandlers;

use App\Application\TiposEvento\DTOs\TipoEventoDTO;
use App\Application\TiposEvento\Queries\GetTipoEventoByIdQuery;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;

class GetTipoEventoByIdQueryHandler
{
    public function __construct(private readonly TipoEventoRepositoryInterface $repository) {}

    public function handle(GetTipoEventoByIdQuery $query): TipoEventoDTO
    {
        return $this->repository->findById($query->id);
    }
}
