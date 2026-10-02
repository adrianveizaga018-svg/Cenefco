<?php

namespace App\Application\Tesis\QueryHandlers;

use App\Application\Tesis\DTOs\TesisDTO;
use App\Application\Tesis\Queries\GetTesisByIdQuery;
use App\Domain\Tesis\Contracts\TesisRepositoryInterface;

class GetTesisByIdQueryHandler
{
    public function __construct(private readonly TesisRepositoryInterface $repository) {}

    public function handle(GetTesisByIdQuery $query): TesisDTO
    {
        return TesisDTO::fromRow($this->repository->findById($query->id));
    }
}
