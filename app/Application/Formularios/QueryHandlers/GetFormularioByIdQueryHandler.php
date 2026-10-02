<?php

namespace App\Application\Formularios\QueryHandlers;

use App\Application\Formularios\DTOs\FormularioDTO;
use App\Application\Formularios\Queries\GetFormularioByIdQuery;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class GetFormularioByIdQueryHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(GetFormularioByIdQuery $query): FormularioDTO
    {
        return $this->repository->findById($query->id);
    }
}
