<?php

namespace App\Application\Resenas\Handlers;

use App\Application\Resenas\Commands\UpdateResenaCommand;
use App\Application\Resenas\DTOs\ResenaDTO;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class UpdateResenaHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateResenaCommand $command): ResenaDTO
    {
        $data = array_filter([
            'estado'         => $command->estado,
            'verificado'     => $command->verificado,
            'destacada'      => $command->destacada,
            'motivo_rechazo' => $command->motivo_rechazo,
            'foto_url'       => $command->foto_url,
            'nombre'         => $command->nombre,
            'cargo_actual'   => $command->cargo_actual,
            'calificacion'   => $command->calificacion,
            'titulo_resena'  => $command->titulo_resena,
            'resena'         => $command->resena,
            'usuario_id'     => $command->usuario_id,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
