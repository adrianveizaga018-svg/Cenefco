<?php

namespace App\Application\Articulos\Handlers;

use App\Application\Articulos\Commands\DeleteArticuloCommand;
use App\Domain\Articulos\Contracts\ArticuloRepositoryInterface;

class DeleteArticuloHandler
{
    public function __construct(private readonly ArticuloRepositoryInterface $repository) {}

    public function handle(DeleteArticuloCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->softDelete($command->id);
    }
}
