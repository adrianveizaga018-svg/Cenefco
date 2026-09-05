<?php

namespace App\Application\Boletines\QueryHandlers;

use App\Application\Boletines\DTOs\BoletinDTO;
use App\Application\Boletines\Queries\GetBoletinByIdQuery;
use App\Domain\Boletines\Contracts\BoletinRepositoryInterface;

class GetBoletinByIdQueryHandler
{
    public function __construct(private readonly BoletinRepositoryInterface $repository) {}

    public function handle(GetBoletinByIdQuery $query): BoletinDTO
    {
        return BoletinDTO::fromRow($this->repository->findById($query->id));
    }
}
