<?php

namespace App\Application\Expedidos\Handlers;

use App\Application\Expedidos\Commands\DeleteExpedidoCommand;
use App\Domain\Expedidos\Contracts\ExpedidoRepositoryInterface;

class DeleteExpedidoHandler
{
    public function __construct(private readonly ExpedidoRepositoryInterface $repository) {}

    public function handle(DeleteExpedidoCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
