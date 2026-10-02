<?php
namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\CreateLeadSeguimientoCommand;
use App\Application\CampanasLeads\DTOs\LeadSeguimientoDTO;
use App\Infrastructure\CampanasLeads\Models\LeadSeguimiento;

class CreateLeadSeguimientoHandler
{
    public function handle(CreateLeadSeguimientoCommand $cmd): LeadSeguimientoDTO
    {
        $model = LeadSeguimiento::create([
            'lead_id'              => $cmd->leadId,
            'vendedor_id'          => $cmd->vendedorId,
            'tipo'                 => $cmd->tipo,
            'resultado'            => $cmd->resultado,
            'nota'                 => $cmd->nota,
            'proxima_accion'       => $cmd->proximaAccion,
            'fecha_proxima_accion' => $cmd->fechaProxima,
        ]);
        return LeadSeguimientoDTO::fromModel($model);
    }
}
