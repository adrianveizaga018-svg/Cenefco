<?php

namespace App\Application\Imparticiones\QueryHandlers;

use App\Application\Imparticiones\DTOs\ImparteDTO;
use App\Application\Imparticiones\Queries\GetImparteByIdQuery;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;

class GetImparteByIdQueryHandler
{
    public function __construct(
        private readonly ImparteRepositoryInterface $repository
    ) {}

    public function handle(GetImparteByIdQuery $query): ImparteDTO
    {
        return $this->repository->findById($query->id);
    }
}
