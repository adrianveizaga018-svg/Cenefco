<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\CreateLeadCommand;
use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;

class CreateLeadHandler
{
    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public function handle(CreateLeadCommand $command): LeadDTO
    {
        return $this->repository->create($command->campanaLeadId, [
            'nombre'    => $command->nombre,
            'celular'   => $command->celular,
            'correo'    => $command->correo,
            'profesion' => $command->profesion,
        ]);
    }
}
