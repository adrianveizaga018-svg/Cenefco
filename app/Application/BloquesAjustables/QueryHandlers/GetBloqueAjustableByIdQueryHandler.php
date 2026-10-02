<?php

namespace App\Application\BloquesAjustables\QueryHandlers;

use App\Application\BloquesAjustables\DTOs\BloqueAjustableDTO;
use App\Application\BloquesAjustables\Queries\GetBloqueAjustableByIdQuery;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;

class GetBloqueAjustableByIdQueryHandler
{
    public function __construct(
        private readonly BloqueAjustableRepositoryInterface $repository,
    ) {}

    public function handle(GetBloqueAjustableByIdQuery $query): BloqueAjustableDTO
    {
        return BloqueAjustableDTO::fromRow($this->repository->findById($query->idBloqueajustable));
    }
}
