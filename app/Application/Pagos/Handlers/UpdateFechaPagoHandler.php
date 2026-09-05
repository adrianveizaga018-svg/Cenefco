<?php

namespace App\Application\Pagos\Handlers;

use App\Application\Pagos\Commands\UpdateFechaPagoCommand;
use App\Application\Pagos\DTOs\FechaPagoDTO;
use App\Domain\Pagos\Contracts\FechaPagoRepositoryInterface;
use App\Domain\Pagos\Exceptions\FechaPagoNotFoundException;

class UpdateFechaPagoHandler
{
    public function __construct(
        private readonly FechaPagoRepositoryInterface $repository,
    ) {}

    public function handle(UpdateFechaPagoCommand $command): FechaPagoDTO
    {
        $model = $this->repository->findById($command->id);
        if (! $model) {
            throw new FechaPagoNotFoundException($command->id);
        }

        $updated = $this->repository->update($command->id, $command->data);
        return FechaPagoDTO::fromModel($updated);
    }
}
