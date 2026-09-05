<?php

declare(strict_types=1);

namespace App\Application\Descargables\QueryHandlers;

use App\Application\Descargables\Queries\GetDescargablesQuery;
use App\Domain\Descargables\Contracts\DescargableRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetDescargablesQueryHandler
{
    public function __construct(
        private readonly DescargableRepositoryInterface $repository,
    ) {}

    public function handle(GetDescargablesQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
