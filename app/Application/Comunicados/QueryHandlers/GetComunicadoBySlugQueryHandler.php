<?php

namespace App\Application\Comunicados\QueryHandlers;

use App\Application\Comunicados\DTOs\ComunicadoDTO;
use App\Application\Comunicados\Queries\GetComunicadoBySlugQuery;
use App\Domain\Comunicados\Contracts\ComunicadoRepositoryInterface;

class GetComunicadoBySlugQueryHandler
{
    public function __construct(
        private readonly ComunicadoRepositoryInterface $repository
    ) {}

    public function handle(GetComunicadoBySlugQuery $query): ComunicadoDTO
    {
        $model = $this->repository->findBySlug($query->slug);

        return ComunicadoDTO::fromModel($model);
    }
}
