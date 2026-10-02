<?php
namespace App\Application\FormulariosAcademicos\QueryHandlers;
use App\Application\FormulariosAcademicos\DTOs\FormularioAcademicoDTO;
use App\Application\FormulariosAcademicos\Queries\GetFormularioAcademicoByIdQuery;
use App\Domain\FormulariosAcademicos\Contracts\FormularioAcademicoRepositoryInterface;
class GetFormularioAcademicoByIdQueryHandler {
    public function __construct(private readonly FormularioAcademicoRepositoryInterface $repository) {}
    public function handle(GetFormularioAcademicoByIdQuery $query): FormularioAcademicoDTO {
        return FormularioAcademicoDTO::fromRow($this->repository->findById($query->idFormulario));
    }
}
