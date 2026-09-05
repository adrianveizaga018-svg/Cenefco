<?php

namespace App\Application\Fotos\QueryHandlers;

use App\Application\Fotos\DTOs\FotoDTO;
use App\Application\Fotos\Queries\GetFotoByIdQuery;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;

class GetFotoByIdQueryHandler
{
    public function __construct(private readonly FotoRepositoryInterface $repository) {}

    public function handle(GetFotoByIdQuery $query): FotoDTO
    {
        return FotoDTO::fromRow($this->repository->findById($query->id));
    }
}
