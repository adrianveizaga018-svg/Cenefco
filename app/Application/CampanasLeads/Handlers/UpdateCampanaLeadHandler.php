<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\UpdateCampanaLeadCommand;
use App\Application\CampanasLeads\DTOs\CampanaLeadDTO;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;

class UpdateCampanaLeadHandler
{
    public function __construct(private readonly CampanaLeadRepositoryInterface $repository) {}

    public function handle(UpdateCampanaLeadCommand $command): CampanaLeadDTO
    {
        $data = array_filter([
            'nombre'       => $command->nombre,
            'descripcion'  => $command->descripcion,
            'estado'       => $command->estado,
            'fecha_inicio' => $command->fecha_inicio,
            'fecha_fin'    => $command->fecha_fin,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
