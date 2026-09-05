<?php

namespace App\Application\Asesores\Handlers;

use App\Application\Asesores\Commands\DeleteAsesorCommand;
use App\Domain\Asesores\Contracts\AsesorRepositoryInterface;

class DeleteAsesorHandler
{
    public function __construct(private readonly AsesorRepositoryInterface $repository) {}

    public function handle(DeleteAsesorCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
