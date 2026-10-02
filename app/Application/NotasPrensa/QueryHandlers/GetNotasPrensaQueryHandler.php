<?php

declare(strict_types=1);

namespace App\Application\NotasPrensa\QueryHandlers;

use App\Application\NotasPrensa\Queries\GetNotasPrensaQuery;
use App\Domain\NotasPrensa\Contracts\NotaPrensaRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetNotasPrensaQueryHandler
{
    public function __construct(
        private readonly NotaPrensaRepositoryInterface $repository,
    ) {}

    public function handle(GetNotasPrensaQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query->pagination, $query->soloDestacadas, $query->soloActivos);
    }
}
