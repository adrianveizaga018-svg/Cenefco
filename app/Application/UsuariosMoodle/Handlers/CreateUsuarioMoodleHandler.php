<?php

namespace App\Application\UsuariosMoodle\Handlers;

use App\Application\UsuariosMoodle\Commands\CreateUsuarioMoodleCommand;
use App\Application\UsuariosMoodle\DTOs\UsuarioMoodleDTO;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;

class CreateUsuarioMoodleHandler
{
    public function __construct(
        private readonly UsuarioMoodleRepositoryInterface $repository,
    ) {}

    public function handle(CreateUsuarioMoodleCommand $command): UsuarioMoodleDTO
    {
        $row = $this->repository->create([
            'id_usmoodle'    => $command->id_usmoodle,
            'id_us'          => $command->id_us,
            'id_us_reg'      => $command->id_us_reg,
            'num_usmoodle'   => $command->num_usmoodle,
            'id_moodle'      => $command->id_moodle,
            'moodle_id_user' => $command->moodle_id_user,
            'estado'         => $command->estado,
            'fecha_reg'      => now(),
        ]);

        return UsuarioMoodleDTO::fromRow($row);
    }
}
