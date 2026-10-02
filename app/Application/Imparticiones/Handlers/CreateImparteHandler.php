<?php

namespace App\Application\Imparticiones\Handlers;

use App\Application\Imparticiones\Commands\CreateImparteCommand;
use App\Application\Imparticiones\DTOs\ImparteDTO;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;

class CreateImparteHandler
{
    public function __construct(
        private readonly ImparteRepositoryInterface $repository
    ) {}

    public function handle(CreateImparteCommand $command): ImparteDTO
    {
        return $this->repository->create([
            'id_imp'             => $command->id_imp,
            'id_us_reg'          => $command->id_us_reg ?? 0,
            'num_imp'            => $command->num_imp ?? 0,
            'periodo'            => $command->periodo,
            'gestion'            => $command->gestion,
            'id_us'              => $command->id_us,
            'id_mat'             => $command->id_mat,
            'paralelo'           => $command->paralelo,
            'cupo'               => $command->cupo,
            'observacion_imp'    => $command->observacion_imp,
            'nro_resolucion_hcu' => $command->nro_resolucion_hcu,
            'id_moodle'          => $command->id_moodle,
            'estado'             => $command->estado,
            'fecha_reg'          => now(),
        ]);
    }
}
