<?php
namespace App\Application\PaginasAcademicas\QueryHandlers;
use App\Application\PaginasAcademicas\DTOs\PaginaAcademicaDTO;
use App\Application\PaginasAcademicas\Queries\GetPaginaAcademicaByIdQuery;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
class GetPaginaAcademicaByIdQueryHandler {
    public function __construct(private readonly PaginaAcademicaRepositoryInterface $repository) {}
    public function handle(GetPaginaAcademicaByIdQuery $query): PaginaAcademicaDTO {
        return PaginaAcademicaDTO::fromRow($this->repository->findById($query->idPagina));
    }
}
