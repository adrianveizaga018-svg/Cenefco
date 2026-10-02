<?php
namespace App\Application\FormulariosIns\QueryHandlers;
use App\Application\FormulariosIns\DTOs\FormularioInsDTO;
use App\Application\FormulariosIns\Queries\GetFormularioInsByIdQuery;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
class GetFormularioInsByIdQueryHandler {
    public function __construct(private readonly FormularioInsRepositoryInterface $repository) {}
    public function handle(GetFormularioInsByIdQuery $query): FormularioInsDTO {
        return FormularioInsDTO::fromRow($this->repository->findById($query->idFormins));
    }
}
