<?php
namespace App\Application\HojasEvaluacion\QueryHandlers;
use App\Application\HojasEvaluacion\DTOs\HojaEvaluacionDTO;
use App\Application\HojasEvaluacion\Queries\GetHojaEvaluacionByIdQuery;
use App\Domain\HojasEvaluacion\Contracts\HojaEvaluacionRepositoryInterface;
class GetHojaEvaluacionByIdQueryHandler {
    public function __construct(private readonly HojaEvaluacionRepositoryInterface $repository) {}
    public function handle(GetHojaEvaluacionByIdQuery $query): HojaEvaluacionDTO {
        return HojaEvaluacionDTO::fromRow($this->repository->findById($query->idHojaevaluacion));
    }
}
