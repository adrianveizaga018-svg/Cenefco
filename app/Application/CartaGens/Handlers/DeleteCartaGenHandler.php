<?php

namespace App\Application\CartaGens\Handlers;

use App\Application\CartaGens\Commands\DeleteCartaGenCommand;
use App\Domain\CartaGens\Contracts\CartaGenRepositoryInterface;

class DeleteCartaGenHandler
{
    public function __construct(private readonly CartaGenRepositoryInterface $repository) {}

    public function handle(DeleteCartaGenCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
