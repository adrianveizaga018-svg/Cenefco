<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\QueryHandlers;

use App\Application\NotasPrensa\DTOs\NotaPrensaDTO;
use App\Application\NotasPrensa\Queries\GetNotaPrensaByIdQuery;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;

class GetNotaPrensaByIdQueryHandler
{
    public function __construct(
        private readonly NotaPrensaRepositoryInterface $repository,
    ) {}

    public function handle(GetNotaPrensaByIdQuery $query): NotaPrensaDTO
    {
        return $this->repository->findById($query->id);
    }
}
