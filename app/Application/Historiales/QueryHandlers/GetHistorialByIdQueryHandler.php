<?php
namespace App\Application\Historiales\QueryHandlers;
use App\Application\Historiales\DTOs\HistorialDTO;
use App\Application\Historiales\Queries\GetHistorialByIdQuery;
use App\Domain\Historiales\Contracts\HistorialRepositoryInterface;
class GetHistorialByIdQueryHandler {
    public function __construct(private readonly HistorialRepositoryInterface $repository) {}
    public function handle(GetHistorialByIdQuery $query): HistorialDTO {
        return HistorialDTO::fromRow($this->repository->findById($query->idHistorial));
    }
}
