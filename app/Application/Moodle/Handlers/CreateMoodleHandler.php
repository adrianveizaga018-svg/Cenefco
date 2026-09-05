<?php

namespace App\Application\Moodle\Handlers;

use App\Application\Moodle\Commands\CreateMoodleCommand;
use App\Application\Moodle\DTOs\MoodleDTO;
use App\Domain\Moodle\Contracts\MoodleRepositoryInterface;

class CreateMoodleHandler
{
    public function __construct(private readonly MoodleRepositoryInterface $repository) {}

    public function handle(CreateMoodleCommand $command): MoodleDTO
    {
        $row = $this->repository->create([
            'id_moodle'             => $command->idMoodle,
            'id_us_reg'             => $command->idUsReg,
            'num_moodle'            => $command->numMoodle ?? 0,
            'titulo_moodle'         => $command->tituloMoodle,
            'cp_moodle_servidor'    => $command->cpMoodleServidor,
            'cp_moodle_base_datos'  => $command->cpMoodleBaseDatos,
            'cp_moodle_usuario_bd'  => $command->cpMoodleUsuarioBd,
            'cp_moodle_contrasena'  => $command->cpMoodleContrasena,
            'cp_url_campus'         => $command->cpUrlCampus,
            'fecha_reg'             => now(),
            'estado'                => 1,
        ]);

        return MoodleDTO::fromRow($row);
    }
}
