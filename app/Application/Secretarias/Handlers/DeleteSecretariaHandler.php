<?php

namespace App\Application\Secretarias\Handlers;

use App\Application\Secretarias\Commands\DeleteSecretariaCommand;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class DeleteSecretariaHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(DeleteSecretariaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
