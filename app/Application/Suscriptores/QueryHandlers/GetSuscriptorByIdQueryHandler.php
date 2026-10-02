<?php

declare(strict_types=1);

namespace App\Application\Suscriptores\QueryHandlers;

use App\Application\Suscriptores\DTOs\SuscriptorDTO;
use App\Application\Suscriptores\Queries\GetSuscriptorByIdQuery;
use App\Domain\Suscriptores\Contracts\SuscriptorRepositoryInterface;

class GetSuscriptorByIdQueryHandler
{
    public function __construct(
        private readonly SuscriptorRepositoryInterface $repository,
    ) {}

    public function handle(GetSuscriptorByIdQuery $query): SuscriptorDTO
    {
        return $this->repository->findById($query->id);
    }
}
