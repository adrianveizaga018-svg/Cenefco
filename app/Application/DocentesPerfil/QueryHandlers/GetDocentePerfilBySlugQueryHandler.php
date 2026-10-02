<?php

namespace App\Application\DocentesPerfil\QueryHandlers;

use App\Application\DocentesPerfil\DTOs\DocentePerfilDTO;
use App\Application\DocentesPerfil\Queries\GetDocentePerfilBySlugQuery;
use App\Domain\DocentesPerfil\Contracts\DocentePerfilRepositoryInterface;

class GetDocentePerfilBySlugQueryHandler
{
    public function __construct(private readonly DocentePerfilRepositoryInterface $repository) {}

    public function handle(GetDocentePerfilBySlugQuery $query): DocentePerfilDTO
    {
        return $this->repository->findBySlug($query->slug);
    }
}
