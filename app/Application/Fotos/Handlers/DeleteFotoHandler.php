<?php

namespace App\Application\Fotos\Handlers;

use App\Application\Fotos\Commands\DeleteFotoCommand;
use App\Domain\Fotos\Contracts\FotoRepositoryInterface;

class DeleteFotoHandler
{
    public function __construct(private readonly FotoRepositoryInterface $repository) {}

    public function handle(DeleteFotoCommand $command): void
    {
        $this->repository->findById($command->id);
        $this->repository->delete($command->id);
    }
}
