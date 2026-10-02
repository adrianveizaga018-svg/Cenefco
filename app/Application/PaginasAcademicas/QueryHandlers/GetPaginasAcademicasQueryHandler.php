<?php
namespace App\Application\PaginasAcademicas\QueryHandlers;
use App\Application\PaginasAcademicas\Queries\GetPaginasAcademicasQuery;
use App\Domain\PaginasAcademicas\Contracts\PaginaAcademicaRepositoryInterface;
class GetPaginasAcademicasQueryHandler {
    public function __construct(private readonly PaginaAcademicaRepositoryInterface $repository) {}
    public function handle(GetPaginasAcademicasQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->conInactivos);
    }
}
