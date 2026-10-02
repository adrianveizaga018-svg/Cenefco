<?php

namespace App\Application\UsuariosTipoPrograma\QueryHandlers;

use App\Application\UsuariosTipoPrograma\Queries\GetUsuariosTipoProgramaQuery;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;

class GetUsuariosTipoProgramaQueryHandler
{
    public function __construct(
        private readonly UsuarioTipoProgramaRepositoryInterface $repository,
    ) {}

    public function handle(GetUsuariosTipoProgramaQuery $query): array
    {
        return $this->repository->paginate(
            $query->pagination,
            $query->idUs,
            $query->idTipoPrograma,
            $query->conInactivos,
        );
    }
}
