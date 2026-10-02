<?php
namespace App\Application\Historiales\QueryHandlers;
use App\Application\Historiales\Queries\GetHistorialesQuery;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
class GetHistorialesQueryHandler {
    public function __construct(private readonly HistorialRepositoryInterface $repository) {}
    public function handle(GetHistorialesQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->idUs, $query->idTiporeferencia, $query->idTipohistorial, $query->conInactivos);
    }
}
