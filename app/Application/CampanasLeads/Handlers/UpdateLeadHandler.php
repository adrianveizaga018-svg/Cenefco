<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\UpdateLeadCommand;
use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;

class UpdateLeadHandler
{
    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public function handle(UpdateLeadCommand $command): LeadDTO
    {
        $data = array_filter([
            'nombre'    => $command->nombre,
            'celular'   => $command->celular,
            'correo'    => $command->correo,
            'profesion' => $command->profesion,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->campanaLeadId, $command->id, $data);
    }
}
