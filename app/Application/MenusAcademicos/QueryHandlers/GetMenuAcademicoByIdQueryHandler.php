<?php
namespace App\Application\MenusAcademicos\QueryHandlers;
use App\Application\MenusAcademicos\DTOs\MenuAcademicoDTO;
use App\Application\MenusAcademicos\Queries\GetMenuAcademicoByIdQuery;
use App\Domain\MenusAcademicos\Contracts\MenuAcademicoRepositoryInterface;
class GetMenuAcademicoByIdQueryHandler {
    public function __construct(private readonly MenuAcademicoRepositoryInterface $repository) {}
    public function handle(GetMenuAcademicoByIdQuery $query): MenuAcademicoDTO {
        return MenuAcademicoDTO::fromRow($this->repository->findById($query->idMen));
    }
}
