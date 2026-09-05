<?php

namespace App\Application\DescuentosPromociones\QueryHandlers;

use App\Application\DescuentosPromociones\DTOs\DescuentoPromocionDTO;
use App\Application\DescuentosPromociones\Queries\GetDescuentoPromocionByIdQuery;
use App\Domain\DescuentosPromociones\Contracts\DescuentoPromocionRepositoryInterface;

class GetDescuentoPromocionByIdQueryHandler
{
    public function __construct(private readonly DescuentoPromocionRepositoryInterface $repository) {}

    public function handle(GetDescuentoPromocionByIdQuery $query): DescuentoPromocionDTO
    {
        return $this->repository->findById($query->id);
    }
}
