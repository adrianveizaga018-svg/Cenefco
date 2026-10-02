<?php

namespace App\Application\DescuentosPromociones\QueryHandlers;

use App\Application\DescuentosPromociones\Queries\GetDescuentosPromocionesQuery;
use App\Domain\DescuentosPromociones\Contracts\DescuentoPromocionRepositoryInterface;

class GetDescuentosPromocionesQueryHandler
{
    public function __construct(private readonly DescuentoPromocionRepositoryInterface $repository) {}

    public function handle(GetDescuentosPromocionesQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->soloActivos);
    }
}
