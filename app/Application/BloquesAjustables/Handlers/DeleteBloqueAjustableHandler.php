<?php

namespace App\Application\BloquesAjustables\Handlers;

use App\Application\BloquesAjustables\Commands\DeleteBloqueAjustableCommand;
use App\Domain\BloquesAjustables\Contracts\BloqueAjustableRepositoryInterface;

class DeleteBloqueAjustableHandler
{
    public function __construct(
        private readonly BloqueAjustableRepositoryInterface $repository,
    ) {}

    public function handle(DeleteBloqueAjustableCommand $command): void
    {
        $this->repository->delete($command->idBloqueajustable);
    }
}
