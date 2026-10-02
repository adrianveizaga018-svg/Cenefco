<?php

namespace App\Application\Carreras\QueryHandlers;

use App\Application\Carreras\DTOs\CarreraDTO;
use App\Application\Carreras\Queries\GetCarreraByIdQuery;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;

class GetCarreraByIdQueryHandler
{
    public function __construct(
        private readonly CarreraRepositoryInterface $repository
    ) {}

    public function handle(GetCarreraByIdQuery $query): CarreraDTO
    {
        return $this->repository->findById($query->id);
    }
}
