<?php

namespace App\Application\Formularios\QueryHandlers;

use App\Application\Formularios\DTOs\FormularioDTO;
use App\Application\Formularios\Queries\GetFormularioBySlugQuery;
use App\Domain\Formularios\Contracts\FormularioRepositoryInterface;

class GetFormularioBySlugQueryHandler
{
    public function __construct(private readonly FormularioRepositoryInterface $repository) {}

    public function handle(GetFormularioBySlugQuery $query): FormularioDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
