<?php

namespace App\Application\Organigramas\Handlers;

use App\Application\Organigramas\Commands\UpdateOrganigramaCommand;
use App\Application\Organigramas\DTOs\OrganigramaDTO;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;

class UpdateOrganigramaHandler
{
    public function __construct(private readonly OrganigramaRepositoryInterface $repository) {}

    public function handle(UpdateOrganigramaCommand $command): OrganigramaDTO
    {
        return $this->repository->update($command->id, $command->data);
    }
}
