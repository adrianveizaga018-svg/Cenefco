<?php

namespace App\Application\Pagos\QueryHandlers;

use App\Application\Pagos\DTOs\PagoDTO;
use App\Application\Pagos\Queries\GetPagoByIdQuery;
use App\Domain\Pagos\Contracts\PagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\PagoNotFoundException;

class GetPagoByIdQueryHandler
{
    public function __construct(
        private readonly PagoRepositoryInterface $repository,
    ) {}

    public function handle(GetPagoByIdQuery $query): PagoDTO
    {
        $model = $this->repository->findById($query->id);
        if (! $model) {
            throw new PagoNotFoundException($query->id);
        }

        return PagoDTO::fromModel($model);
    }
}
