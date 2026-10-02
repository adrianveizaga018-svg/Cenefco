<?php

namespace App\Application\DirectorioInstitucional\QueryHandlers;

use App\Application\DirectorioInstitucional\DTOs\DirectorioInstitucionalDTO;
use App\Application\DirectorioInstitucional\Queries\GetDirectorioByIdQuery;
use App\Domain\DirectorioInstitucional\Contracts\DirectorioInstitucionalRepositoryInterface;

class GetDirectorioByIdQueryHandler
{
    public function __construct(
        private readonly DirectorioInstitucionalRepositoryInterface $repository
    ) {}

    public function handle(GetDirectorioByIdQuery $query): DirectorioInstitucionalDTO
    {
        return $this->repository->findById($query->id);
    }
}
