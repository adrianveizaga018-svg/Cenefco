<?php

namespace App\Application\Normas\Handlers;

use App\Application\Normas\Commands\DeleteNormaCommand;
use App\Domain\Normas\Contracts\NormaRepositoryInterface;

class DeleteNormaHandler
{
    public function __construct(private readonly NormaRepositoryInterface $repository) {}

    public function handle(DeleteNormaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
