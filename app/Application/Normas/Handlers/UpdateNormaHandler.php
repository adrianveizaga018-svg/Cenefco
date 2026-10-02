<?php

namespace App\Application\Normas\Handlers;

use App\Application\Normas\Commands\UpdateNormaCommand;
use App\Application\Normas\DTOs\NormaDTO;
use App\Domain\Normas\Contracts\NormaRepositoryInterface;

class UpdateNormaHandler
{
    public function __construct(private readonly NormaRepositoryInterface $repository) {}

    public function handle(UpdateNormaCommand $command): NormaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
