<?php

namespace App\Application\CartaModelos\Handlers;

use App\Application\CartaModelos\Commands\DeleteCartaModeloCommand;
use App\Domain\CartaModelos\Contracts\CartaModeloRepositoryInterface;

class DeleteCartaModeloHandler
{
    public function __construct(private readonly CartaModeloRepositoryInterface $repository) {}

    public function handle(DeleteCartaModeloCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
