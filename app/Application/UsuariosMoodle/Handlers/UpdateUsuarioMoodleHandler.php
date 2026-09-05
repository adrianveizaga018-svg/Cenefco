<?php

namespace App\Application\UsuariosMoodle\Handlers;

use App\Application\UsuariosMoodle\Commands\UpdateUsuarioMoodleCommand;
use App\Application\UsuariosMoodle\DTOs\UsuarioMoodleDTO;
use App\Domain\UsuariosMoodle\Contracts\UsuarioMoodleRepositoryInterface;

class UpdateUsuarioMoodleHandler
{
    public function __construct(
        private readonly UsuarioMoodleRepositoryInterface $repository,
    ) {}

    public function handle(UpdateUsuarioMoodleCommand $command): UsuarioMoodleDTO
    {
        $data = array_filter([
            'id_moodle'      => $command->id_moodle,
            'moodle_id_user' => $command->moodle_id_user,
            'estado'         => $command->estado,
        ], fn ($v) => $v !== null);

        $row = $this->repository->update($command->id, $data);

        return UsuarioMoodleDTO::fromRow($row);
    }
}
