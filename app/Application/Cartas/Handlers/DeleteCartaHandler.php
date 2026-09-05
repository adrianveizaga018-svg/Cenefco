<?php

namespace App\Application\Cartas\Handlers;

use App\Application\Cartas\Commands\DeleteCartaCommand;
use App\Domain\Cartas\Contracts\CartaRepositoryInterface;

class DeleteCartaHandler
{
    public function __construct(private readonly CartaRepositoryInterface $repository) {}

    public function handle(DeleteCartaCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
