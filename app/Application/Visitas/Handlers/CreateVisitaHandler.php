<?php

namespace App\Application\Visitas\Handlers;

use App\Application\Visitas\Commands\CreateVisitaCommand;
use App\Application\Visitas\DTOs\VisitaDTO;
use App\Domain\Visitas\Contracts\VisitaRepositoryInterface;

class CreateVisitaHandler
{
    public function __construct(
        private readonly VisitaRepositoryInterface $repository
    ) {}

    public function handle(CreateVisitaCommand $command): VisitaDTO
    {
        return $this->repository->create([
            'session_id'   => $command->session_id,
            'url'          => $command->url,
            'ruta'         => $command->ruta,
            'titulo'       => $command->titulo,
            'referrer'     => $command->referrer,
            'pais'         => $command->pais,
            'ciudad'       => $command->ciudad,
            'dispositivo'  => $command->dispositivo,
            'navegador'    => $command->navegador,
            'so'           => $command->so,
            'duracion_seg' => $command->duracion_seg,
        ]);
    }
}
