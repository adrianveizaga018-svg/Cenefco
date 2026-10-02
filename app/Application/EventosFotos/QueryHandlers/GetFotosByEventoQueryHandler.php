<?php

namespace App\Application\EventosFotos\QueryHandlers;

use App\Application\EventosFotos\Queries\GetFotosByEventoQuery;
use App\Domain\EventosFotos\Contracts\EventoFotoRepositoryInterface;

class GetFotosByEventoQueryHandler
{
    public function __construct(private readonly EventoFotoRepositoryInterface $repository) {}

    public function handle(GetFotosByEventoQuery $query): array
    {
        return $this->repository->findByEvento($query->eventoId);
    }
}
