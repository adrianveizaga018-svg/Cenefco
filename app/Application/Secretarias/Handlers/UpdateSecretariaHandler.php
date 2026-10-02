<?php

namespace App\Application\Secretarias\Handlers;

use App\Application\Secretarias\Commands\UpdateSecretariaCommand;
use App\Application\Secretarias\DTOs\SecretariaDTO;
use App\Domain\Secretarias\Contracts\SecretariaRepositoryInterface;

class UpdateSecretariaHandler
{
    public function __construct(private readonly SecretariaRepositoryInterface $repository) {}

    public function handle(UpdateSecretariaCommand $command): SecretariaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
