<?php

namespace App\Application\Organigramas\Handlers;

use App\Application\Organigramas\Commands\DeleteOrganigramaCommand;
use App\Domain\Organigramas\Contracts\OrganigramaRepositoryInterface;

class DeleteOrganigramaHandler
{
    public function __construct(private readonly OrganigramaRepositoryInterface $repository) {}

    public function handle(DeleteOrganigramaCommand $command): bool
    {
        return $this->repository->delete($command->id);
    }
}
