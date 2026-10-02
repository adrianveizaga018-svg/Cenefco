<?php
namespace App\Application\MenusAcademicos\QueryHandlers;
use App\Application\MenusAcademicos\Queries\GetMenusAcademicosQuery;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
class GetMenusAcademicosQueryHandler {
    public function __construct(private readonly MenuAcademicoRepositoryInterface $repository) {}
    public function handle(GetMenusAcademicosQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->idMod, $query->conInactivos);
    }
}
