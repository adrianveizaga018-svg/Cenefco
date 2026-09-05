<?php

namespace App\Application\TiposEvento\QueryHandlers;

use App\Application\TiposEvento\Queries\GetTiposEventoQuery;
use App\Domain\TiposEvento\Contracts\TipoEventoRepositoryInterface;

class GetTiposEventoQueryHandler
{
    public function __construct(private readonly TipoEventoRepositoryInterface $repository) {}

    public function handle(GetTiposEventoQuery $query): array
    {
        return $this->repository->paginate($query->pagination);
    }
}
