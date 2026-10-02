<?php

namespace App\Application\UsuariosPlan\QueryHandlers;

use App\Application\UsuariosPlan\DTOs\UsuarioPlanDTO;
use App\Application\UsuariosPlan\Queries\GetUsuarioPlanByIdQuery;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;

class GetUsuarioPlanByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioPlanRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuarioPlanByIdQuery $query): UsuarioPlanDTO
    {
        return UsuarioPlanDTO::fromRow($this->repository->findById($query->id));
    }
}
