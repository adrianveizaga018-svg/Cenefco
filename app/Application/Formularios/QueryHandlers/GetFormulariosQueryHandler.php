<?php

namespace App\Application\Formularios\QueryHandlers;

use App\Application\Formularios\Queries\GetFormulariosQuery;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class GetFormulariosQueryHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(GetFormulariosQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
