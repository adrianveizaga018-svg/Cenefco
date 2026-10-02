<?php

namespace App\Application\Intents\QueryHandlers;

use App\Application\Intents\DTOs\IntentDTO;
use App\Application\Intents\Queries\GetIntentByIdQuery;
use App\Domain\Intents\Contracts\IntentRepositoryInterface;

class GetIntentByIdQueryHandler
{
    public function __construct(private readonly IntentRepositoryInterface $repository) {}

    public function handle(GetIntentByIdQuery $query): IntentDTO
    {
        return IntentDTO::fromRow($this->repository->findById($query->id));
    }
}
