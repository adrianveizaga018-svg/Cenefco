<?php
namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\UpdateLeadEstadoCommand;
use App\Application\CampanasLeads\DTOs\LeadDTO;
use App\Domain\CampanasLeads\Contracts\LeadRepositoryInterface;

class UpdateLeadEstadoHandler
{
    public function __construct(private readonly LeadRepositoryInterface $repository) {}

    public function handle(UpdateLeadEstadoCommand $cmd): LeadDTO
    {
        return $this->repository->update($cmd->campanaLeadId, $cmd->id, [
            'estado'               => $cmd->estado,
            'vendedor_asignado_id' => $cmd->vendedorAsignadoId,
            'programa_id'          => $cmd->programaId,
        ]);
    }
}
