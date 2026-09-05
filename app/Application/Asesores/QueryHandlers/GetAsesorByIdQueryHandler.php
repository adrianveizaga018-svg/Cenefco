<?php

namespace App\Application\Asesores\QueryHandlers;

use App\Application\Asesores\DTOs\AsesorDTO;
use App\Application\Asesores\Queries\GetAsesorByIdQuery;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;

class GetAsesorByIdQueryHandler
{
    public function __construct(private readonly AsesorRepositoryInterface $repository) {}

    public function handle(GetAsesorByIdQuery $query): AsesorDTO
    {
        return AsesorDTO::fromRow($this->repository->findById($query->id));
    }
}
