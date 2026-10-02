<?php

namespace App\Application\UsuariosPlanDoc\QueryHandlers;

use App\Application\UsuariosPlanDoc\DTOs\UsuarioPlanDocDTO;
use App\Application\UsuariosPlanDoc\Queries\GetUsuarioPlanDocByIdQuery;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;

class GetUsuarioPlanDocByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioPlanDocRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuarioPlanDocByIdQuery $query): UsuarioPlanDocDTO
    {
        return UsuarioPlanDocDTO::fromRow($this->repository->findById($query->id));
    }
}
