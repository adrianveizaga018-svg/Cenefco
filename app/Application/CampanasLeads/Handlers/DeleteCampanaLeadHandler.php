<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\DeleteCampanaLeadCommand;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;

class DeleteCampanaLeadHandler
{
    public function __construct(private readonly CampanaLeadRepositoryInterface $repository) {}

    public function handle(DeleteCampanaLeadCommand $command): void
    {
        $this->repository->delete($command->id);
    }
}
