<?php

namespace App\Application\UsuariosMoodle\Handlers;

use App\Application\UsuariosMoodle\Commands\DeleteUsuarioMoodleCommand;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;

class DeleteUsuarioMoodleHandler
{
    public function __construct(
        private readonly UsuarioMoodleRepositoryInterface $repository,
    ) {}

    public function handle(DeleteUsuarioMoodleCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
