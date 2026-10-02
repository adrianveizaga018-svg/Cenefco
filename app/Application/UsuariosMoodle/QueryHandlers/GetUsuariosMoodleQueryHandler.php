<?php

namespace App\Application\UsuariosMoodle\QueryHandlers;

use App\Application\UsuariosMoodle\Queries\GetUsuariosMoodleQuery;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;

class GetUsuariosMoodleQueryHandler
{
    public function __construct(
        private readonly UsuarioMoodleRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuariosMoodleQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idUs,
            $query->idMoodle,
            $query->conInactivos,
        );
    }
}
