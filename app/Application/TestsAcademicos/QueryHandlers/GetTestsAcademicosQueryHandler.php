<?php
namespace App\Application\TestsAcademicos\QueryHandlers;
use App\Application\TestsAcademicos\Queries\GetTestsAcademicosQuery;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
class GetTestsAcademicosQueryHandler {
    public function __construct(private readonly TestAcademicoRepositoryInterface $repository) {}
    public function handle(GetTestsAcademicosQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->habilitarTest, $query->conInactivos);
    }
}
