<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\DeleteLeadCommand;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;

class DeleteLeadHandler
{
    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public function handle(DeleteLeadCommand $command): void
    {
        $this->repository->delete($command->campanaLeadId, $command->id);
    }
}
