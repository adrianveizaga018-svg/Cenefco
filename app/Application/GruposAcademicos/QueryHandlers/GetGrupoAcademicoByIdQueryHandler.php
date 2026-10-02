<?php
namespace App\Application\GruposAcademicos\QueryHandlers;
use App\Application\GruposAcademicos\DTOs\GrupoAcademicoDTO;
use App\Application\GruposAcademicos\Queries\GetGrupoAcademicoByIdQuery;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
class GetGrupoAcademicoByIdQueryHandler {
    public function __construct(private readonly GrupoAcademicoRepositoryInterface $repository) {}
    public function handle(GetGrupoAcademicoByIdQuery $query): GrupoAcademicoDTO {
        return GrupoAcademicoDTO::fromRow($this->repository->findById($query->idGrupo));
    }
}
