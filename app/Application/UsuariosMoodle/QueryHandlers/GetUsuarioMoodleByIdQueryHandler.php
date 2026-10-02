<?php

namespace App\Application\UsuariosMoodle\QueryHandlers;

use App\Application\UsuariosMoodle\DTOs\UsuarioMoodleDTO;
use App\Application\UsuariosMoodle\Queries\GetUsuarioMoodleByIdQuery;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;

class GetUsuarioMoodleByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioMoodleRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuarioMoodleByIdQuery $query): UsuarioMoodleDTO
    {
        return UsuarioMoodleDTO::fromRow($this->repository->findById($query->id));
    }
}
