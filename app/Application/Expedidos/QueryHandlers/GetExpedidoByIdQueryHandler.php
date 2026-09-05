<?php

namespace App\Application\Expedidos\QueryHandlers;

use App\Application\Expedidos\DTOs\ExpedidoDTO;
use App\Application\Expedidos\Queries\GetExpedidoByIdQuery;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;

class GetExpedidoByIdQueryHandler
{
    public function __construct(private readonly ExpedidoRepositoryInterface $repository) {}

    public function handle(GetExpedidoByIdQuery $query): ExpedidoDTO
    {
        return ExpedidoDTO::fromRow($this->repository->findById($query->id));
    }
}
