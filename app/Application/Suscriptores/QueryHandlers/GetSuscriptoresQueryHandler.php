<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\QueryHandlers;

use App\Application\Suscriptores\Queries\GetSuscriptoresQuery;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSuscriptoresQueryHandler
{
    public function __construct(
        private readonly SuscriptorRepositoryInterface $repository,
    ) {}

    public function handle(GetSuscriptoresQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate($query->pagination);
    }
}
