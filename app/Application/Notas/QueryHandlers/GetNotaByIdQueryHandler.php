<?php

namespace App\Application\Notas\QueryHandlers;

use App\Application\Notas\DTOs\NotaDTO;
use App\Application\Notas\Queries\GetNotaByIdQuery;
use App\Domain\Notas\Contracts\NotaRepositoryInterface;

class GetNotaByIdQueryHandler
{
    public function __construct(
        private readonly NotaRepositoryInterface $repository
    ) {}

    public function handle(GetNotaByIdQuery $query): NotaDTO
    {
        return $this->repository->findById($query->id);
    }
}
