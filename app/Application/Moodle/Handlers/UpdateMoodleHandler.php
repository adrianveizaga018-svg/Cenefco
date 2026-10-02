<?php

namespace App\Application\Moodle\Handlers;

use App\Application\Moodle\Commands\UpdateMoodleCommand;
use App\Application\Moodle\DTOs\MoodleDTO;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;

class UpdateMoodleHandler
{
    public function __construct(private readonly MoodleRepositoryInterface $repository) {}

    public function handle(UpdateMoodleCommand $command): MoodleDTO
    {
        $this->repository->update($command->idMoodle, array_filter([
            'titulo_moodle'         => $command->tituloMoodle,
            'cp_moodle_servidor'    => $command->cpMoodleServidor,
            'cp_moodle_base_datos'  => $command->cpMoodleBaseDatos,
            'cp_moodle_usuario_bd'  => $command->cpMoodleUsuarioBd,
            'cp_moodle_contrasena'  => $command->cpMoodleContrasena,
            'cp_url_campus'         => $command->cpUrlCampus,
            'estado'                => $command->estado,
        ], fn ($v) => $v !== null));

        return MoodleDTO::fromRow($this->repository->findById($command->idMoodle));
    }
}
