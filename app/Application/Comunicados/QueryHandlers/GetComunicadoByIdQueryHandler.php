<?php

namespace App\Application\Comunicados\QueryHandlers;

use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Application\Comunicados\Queries\GetComunicadoByIdQuery;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;

class GetComunicadoByIdQueryHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(GetComunicadoByIdQuery $query): ComunicadoDTO
    {
        $model = $this->repository->findById($query->id);

        return ComunicadoDTO::fromModel($model);
    }
}
