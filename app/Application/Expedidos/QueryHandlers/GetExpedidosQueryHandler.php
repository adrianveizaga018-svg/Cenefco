<?php

namespace App\Application\Expedidos\QueryHandlers;

use App\Application\Expedidos\Queries\GetExpedidosQuery;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;

class GetExpedidosQueryHandler
{
    public function __construct(private readonly ExpedidoRepositoryInterface $repository) {}

    public function handle(GetExpedidosQuery $query): array
    {
        return $this->repository->paginate($query->pagination, $query->query);
    }
}
