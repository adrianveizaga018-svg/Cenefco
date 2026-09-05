<?php
namespace App\Application\ModulosAcademicos\QueryHandlers;
use App\Application\ModulosAcademicos\Queries\GetModulosAcademicosQuery;
use App\Domain\ModulosAcademicos\Contracts\ModuloAcademicoRepositoryInterface;
class GetModulosAcademicosQueryHandler {
    public function __construct(private readonly ModuloAcademicoRepositoryInterface $repository) {}
    public function handle(GetModulosAcademicosQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->posicion, $query->conInactivos);
    }
}
