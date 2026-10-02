<?php
namespace App\Application\ModulosAcademicos\QueryHandlers;
use App\Application\ModulosAcademicos\DTOs\ModuloAcademicoDTO;
use App\Application\ModulosAcademicos\Queries\GetModuloAcademicoByIdQuery;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
class GetModuloAcademicoByIdQueryHandler {
    public function __construct(private readonly ModuloAcademicoRepositoryInterface $repository) {}
    public function handle(GetModuloAcademicoByIdQuery $query): ModuloAcademicoDTO {
        return ModuloAcademicoDTO::fromRow($this->repository->findById($query->idMod));
    }
}
