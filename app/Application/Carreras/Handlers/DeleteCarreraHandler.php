<?php

namespace App\Application\Carreras\Handlers;

use App\Application\Carreras\Commands\DeleteCarreraCommand;
use App\Domain\Carreras\Contracts\CarreraRepositoryInterface;

class DeleteCarreraHandler
{
    public function __construct(
        private readonly CarreraRepositoryInterface $repository
    ) {}

    public function handle(DeleteCarreraCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
