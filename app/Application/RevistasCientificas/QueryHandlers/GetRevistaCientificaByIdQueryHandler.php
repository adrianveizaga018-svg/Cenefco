<?php

namespace App\Application\RevistasCientificas\QueryHandlers;

use App\Application\RevistasCientificas\DTOs\RevistaCientificaDTO;
use App\Application\RevistasCientificas\Queries\GetRevistaCientificaByIdQuery;
use App\Domain\RevistasCientificas\Contracts\RevistaCientificaRepositoryInterface;

class GetRevistaCientificaByIdQueryHandler
{
    public function __construct(private readonly RevistaCientificaRepositoryInterface $repository) {}

    public function handle(GetRevistaCientificaByIdQuery $query): RevistaCientificaDTO
    {
        return RevistaCientificaDTO::fromRow($this->repository->findById($query->id));
    }
}
