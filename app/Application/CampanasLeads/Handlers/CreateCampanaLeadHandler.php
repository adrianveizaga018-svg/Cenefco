<?php

namespace App\Application\CampanasLeads\Handlers;

use App\Application\CampanasLeads\Commands\CreateCampanaLeadCommand;
use App\Application\CampanasLeads\DTOs\CampanaLeadDTO;
use App\Domain\CampanasLeads\Contracts\CampanaLeadRepositoryInterface;

class CreateCampanaLeadHandler
{
    public function __construct(private readonly CampanaLeadRepositoryInterface $repository) {}

    public function handle(CreateCampanaLeadCommand $command): CampanaLeadDTO
    {
        return $this->repository->create([
            'nombre'       => $command->nombre,
            'descripcion'  => $command->descripcion,
            'estado'       => $command->estado,
            'fecha_inicio' => $command->fecha_inicio,
            'fecha_fin'    => $command->fecha_fin,
        ]);
    }
}
