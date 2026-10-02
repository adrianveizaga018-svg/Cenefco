<?php

namespace App\Application\Usuarios\QueryHandlers;

use App\Application\Usuarios\DTOs\UserDTO;
use App\Application\Usuarios\Queries\GetUsuarioByIdQuery;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;

class GetUsuarioByIdQueryHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function handle(GetUsuarioByIdQuery $query): UserDTO
    {
        return $this->repository->findById($query->id);
    }
}
