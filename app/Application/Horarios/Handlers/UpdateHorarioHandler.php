<?php

namespace App\Application\Horarios\Handlers;

use App\Application\Horarios\Commands\UpdateHorarioCommand;
use App\Application\Horarios\DTOs\HorarioDTO;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;

class UpdateHorarioHandler
{
    public function __construct(
        private readonly HorarioRepositoryInterface $repository
    ) {}

    public function handle(UpdateHorarioCommand $command): HorarioDTO
    {
        $data = array_filter([
            'id_d'        => $command->id_d,
            'hora_inicio' => $command->hora_inicio,
            'hora_fin'    => $command->hora_fin,
            'periodos'    => $command->periodos,
            'estado'      => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
