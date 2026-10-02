<?php
namespace App\Application\FormulariosIns\QueryHandlers;
use App\Application\FormulariosIns\Queries\GetFormulariosInsQuery;
use App\Domain\FormulariosIns\Contracts\FormularioInsRepositoryInterface;
class GetFormulariosInsQueryHandler {
    public function __construct(private readonly FormularioInsRepositoryInterface $repository) {}
    public function handle(GetFormulariosInsQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->query, $query->idImp, $query->conInactivos);
    }
}
