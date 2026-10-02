<?php
namespace App\Application\HojasEvaluacion\QueryHandlers;
use App\Application\HojasEvaluacion\Queries\GetHojasEvaluacionQuery;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
class GetHojasEvaluacionQueryHandler {
    public function __construct(private readonly HojaEvaluacionRepositoryInterface $repository) {}
    public function handle(GetHojasEvaluacionQuery $query): array {
        return $this->repository->paginate($query->pagination, $query->idUs, $query->idUsTut, $query->conInactivos);
    }
}
