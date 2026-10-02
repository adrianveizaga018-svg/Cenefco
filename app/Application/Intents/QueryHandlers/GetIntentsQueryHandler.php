<?php

namespace App\Application\Intents\QueryHandlers;

use App\Application\Intents\DTOs\IntentDTO;
use App\Application\Intents\Queries\GetIntentsQuery;
use App\Domain\Intents\Contracts\IntentRepositoryInterface;

class GetIntentsQueryHandler
{
    public function __construct(private readonly IntentRepositoryInterface $repository) {}

    public function handle(GetIntentsQuery $query): array
    {
        $rows = $this->repository->index($query->query, $query->dominio);

        return [
            'data'  => array_map(fn ($r) => IntentDTO::fromRow($r), $rows),
            'total' => count($rows),
        ];
    }
}
