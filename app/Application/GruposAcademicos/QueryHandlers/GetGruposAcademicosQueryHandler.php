<?php
namespace App\Application\GruposAcademicos\QueryHandlers;
use App\Application\GruposAcademicos\Queries\GetGruposAcademicosQuery;
use App\Domain\GruposAcademicos\Contracts\GrupoAcademicoRepositoryInterface;
class GetGruposAcademicosQueryHandler {
    public function __construct(private readonly GrupoAcademicoRepositoryInterface $repository) {}
    public function handle(GetGruposAcademicosQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->idTest, $query->conInactivos);
    }
}
