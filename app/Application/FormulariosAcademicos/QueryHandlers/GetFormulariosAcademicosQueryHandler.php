<?php
namespace App\Application\FormulariosAcademicos\QueryHandlers;
use App\Application\FormulariosAcademicos\Queries\GetFormulariosAcademicosQuery;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
class GetFormulariosAcademicosQueryHandler {
    public function __construct(private readonly FormularioAcademicoRepositoryInterface $repository) {}
    public function handle(GetFormulariosAcademicosQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
