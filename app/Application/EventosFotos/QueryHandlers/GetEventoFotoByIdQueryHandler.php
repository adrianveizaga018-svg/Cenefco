<?php

namespace App\Application\EventosFotos\QueryHandlers;

use App\Application\EventosFotos\DTOs\EventoFotoDTO;
use App\Application\EventosFotos\Queries\GetEventoFotoByIdQuery;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;

class GetEventoFotoByIdQueryHandler
{
    public function __construct(private readonly EventoFotoRepositoryInterface $repository) {}

    public function handle(GetEventoFotoByIdQuery $query): EventoFotoDTO
    {
        return $this->repository->findById($query->id);
    }
}
