<?php
namespace App\Application\TestsAcademicos\QueryHandlers;
use App\Application\TestsAcademicos\DTOs\TestAcademicoDTO;
use App\Application\TestsAcademicos\Queries\GetTestAcademicoByIdQuery;
use App\Domain\TestsAcademicos\Contracts\TestAcademicoRepositoryInterface;
class GetTestAcademicoByIdQueryHandler {
    public function __construct(private readonly TestAcademicoRepositoryInterface $repository) {}
    public function handle(GetTestAcademicoByIdQuery $query): TestAcademicoDTO {
        return TestAcademicoDTO::fromRow($this->repository->findById($query->idTest));
    }
}
