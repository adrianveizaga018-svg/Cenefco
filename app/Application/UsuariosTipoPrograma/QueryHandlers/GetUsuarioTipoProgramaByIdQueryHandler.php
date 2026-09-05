<?php

namespace App\Application\UsuariosTipoPrograma\QueryHandlers;

use App\Application\UsuariosTipoPrograma\DTOs\UsuarioTipoProgramaDTO;
use App\Application\UsuariosTipoPrograma\Queries\GetUsuarioTipoProgramaByIdQuery;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;

class GetUsuarioTipoProgramaByIdQueryHandler
{
    public function __construct(
        private readonly UsuarioTipoProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuarioTipoProgramaByIdQuery $query): UsuarioTipoProgramaDTO
    {
        return UsuarioTipoProgramaDTO::fromRow($this->repository->findById($query->id));
    }
}
