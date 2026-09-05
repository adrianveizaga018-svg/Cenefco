<?php

namespace App\Application\Horarios\Handlers;

use App\Application\Horarios\Commands\CreateHorarioCommand;
use App\Application\Horarios\DTOs\HorarioDTO;
use App\Domain\Horarios\Contracts\HorarioRepositoryInterface;

class CreateHorarioHandler
{
    public function __construct(
        private readonly HorarioRepositoryInterface $repository
    ) {}

    public function handle(CreateHorarioCommand $command): HorarioDTO
    {
        return $this->repository->create([
            'id_horar'    => $command->id_horar,
            'id_us_reg'   => $command->id_us_reg ?? 0,
            'id_imp'      => $command->id_imp,
            'id_d'        => $command->id_d,
            'hora_inicio' => $command->hora_inicio,
            'hora_fin'    => $command->hora_fin,
            'periodos'    => $command->periodos,
            'estado'      => $command->estado,
            'fecha_reg'   => now(),
        ]);
    }
}
