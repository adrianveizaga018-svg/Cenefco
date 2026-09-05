<?php

namespace App\Application\Imparticiones\Handlers;

use App\Application\Imparticiones\Commands\UpdateImparteCommand;
use App\Application\Imparticiones\DTOs\ImparteDTO;
use App\Domain\Imparticiones\Contracts\ImparteRepositoryInterface;

class UpdateImparteHandler
{
    public function __construct(
        private readonly ImparteRepositoryInterface $repository
    ) {}

    public function handle(UpdateImparteCommand $command): ImparteDTO
    {
        $data = array_filter([
            'periodo'            => $command->periodo,
            'gestion'            => $command->gestion,
            'id_us'              => $command->id_us,
            'id_mat'             => $command->id_mat,
            'paralelo'           => $command->paralelo,
            'cupo'               => $command->cupo,
            'observacion_imp'    => $command->observacion_imp,
            'nro_resolucion_hcu' => $command->nro_resolucion_hcu,
            'estado'             => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
