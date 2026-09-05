<?php

namespace App\Application\Ayudas\QueryHandlers;

use App\Application\Ayudas\DTOs\AyudaDTO;
use App\Application\Ayudas\Queries\GetAyudaByIdQuery;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;

class GetAyudaByIdQueryHandler
{
    public function __construct(private readonly AyudaRepositoryInterface $repository) {}

    public function handle(GetAyudaByIdQuery $query): AyudaDTO
    {
        return AyudaDTO::fromRow($this->repository->findById($query->id));
    }
}
