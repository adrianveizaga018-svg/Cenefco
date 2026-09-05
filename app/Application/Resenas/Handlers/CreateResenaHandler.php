<?php

namespace App\Application\Resenas\Handlers;

use App\Application\Resenas\Commands\CreateResenaCommand;
use App\Application\Resenas\DTOs\ResenaDTO;
use App\Domain\Resenas\Contracts\ResenaRepositoryInterface;

class CreateResenaHandler
{
    public function __construct(
        private readonly ResenaRepositoryInterface $repository,
    ) {}

    public function handle(CreateResenaCommand $command): ResenaDTO
    {
        return $this->repository->create([
            'programa_id'   => $command->programa_id,
            'usuario_id'    => $command->usuario_id,
            'nombre'        => $command->nombre,
            'cargo_actual'  => $command->cargo_actual,
            'foto_url'      => $command->foto_url,
            'calificacion'  => $command->calificacion,
            'titulo_resena' => $command->titulo_resena,
            'resena'        => $command->resena,
            'destacada'     => $command->destacada,
            'estado'        => 'pendiente',
            'verificado'    => false,
        ]);
    }
}
