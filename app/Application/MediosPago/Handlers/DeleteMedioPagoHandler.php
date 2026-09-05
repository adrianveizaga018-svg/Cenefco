<?php

namespace App\Application\MediosPago\Handlers;

use App\Application\MediosPago\Commands\DeleteMedioPagoCommand;
use App\Domain\MediosPago\Contracts\MedioPagoRepositoryInterface;

class DeleteMedioPagoHandler
{
    public function __construct(private readonly MedioPagoRepositoryInterface $repository) {}

    public function handle(DeleteMedioPagoCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
