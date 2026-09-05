<?php

namespace App\Application\SpeechVentas\QueryHandlers;

use App\Application\SpeechVentas\Queries\GetSpeechVentasQuery;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;

class GetSpeechVentasQueryHandler
{
    public function __construct(private readonly SpeechVentasRepositoryInterface $repository) {}

    public function handle(GetSpeechVentasQuery $query): array
    {
        return $this->repository->paginate(
            $query->pageIndex,
            $query->pageSize,
            $query->query,
            $query->categoria,
            $query->activo,
        );
    }
}
