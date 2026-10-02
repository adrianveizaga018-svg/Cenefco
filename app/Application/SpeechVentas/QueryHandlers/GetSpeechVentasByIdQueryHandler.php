<?php

namespace App\Application\SpeechVentas\QueryHandlers;

use App\Application\SpeechVentas\DTOs\SpeechVentasDTO;
use App\Application\SpeechVentas\Queries\GetSpeechVentasByIdQuery;
use App\Domain\SpeechVentas\Contracts\SpeechVentasRepositoryInterface;

class GetSpeechVentasByIdQueryHandler
{
    public function __construct(private readonly SpeechVentasRepositoryInterface $repository) {}

    public function handle(GetSpeechVentasByIdQuery $query): SpeechVentasDTO
    {
        return SpeechVentasDTO::fromRow($this->repository->findById($query->id));
    }
}
