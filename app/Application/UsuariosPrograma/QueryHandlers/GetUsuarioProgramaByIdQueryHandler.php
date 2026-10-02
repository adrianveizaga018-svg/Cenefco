<?php

namespace App\Application\UsuariosPrograma\QueryHandlers;

use App\Application\UsuariosPrograma\DTOs\UsuarioProgramaDTO;
use App\Application\UsuariosPrograma\Queries\GetUsuarioProgramaByIdQuery;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;

class GetUsuarioProgramaByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuarioProgramaByIdQuery $query): UsuarioProgramaDTO
    {
        return UsuarioProgramaDTO::fromRow($this->repository->findById($query->id));
    }
}
