<?php

namespace App\Application\CorreosEnviados\QueryHandlers;

use App\Application\CorreosEnviados\Queries\GetCorreosEnviadosQuery;
use App\Domain\CorreosEnviados\Contracts\CorreoEnviadoRepositoryInterface;

class GetCorreosEnviadosQueryHandler
{
    public function __construct(
        private readonly CorreoEnviadoRepositoryInterface $repository,
    ) {}

    public function handle(GetCorreosEnviadosQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->referenciaTipo,
            $query->referenciaId,
        );
    }
}
