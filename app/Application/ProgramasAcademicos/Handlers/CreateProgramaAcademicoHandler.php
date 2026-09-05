<?php

namespace App\Application\ProgramasAcademicos\Handlers;

use App\Application\ProgramasAcademicos\Commands\CreateProgramaAcademicoCommand;
use App\Application\ProgramasAcademicos\DTOs\ProgramaAcademicoDTO;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;

class CreateProgramaAcademicoHandler
{
    public function __construct(
        private readonly ProgramaAcademicoRepositoryInterface $repository
    ) {}

    public function handle(CreateProgramaAcademicoCommand $command): ProgramaAcademicoDTO
    {
        return $this->repository->create([
            'id_programa'              => $command->id_programa,
            'id_us_reg'                => $command->id_us_reg ?? 0,
            'num_programa'             => $command->num_programa ?? 0,
            'nombre_programa'          => $command->nombre_programa,
            'descripcion'              => $command->descripcion,
            'foto'                     => $command->foto,
            'inicio_actividades'       => $command->inicio_actividades,
            'finalizacion_actividades' => $command->finalizacion_actividades,
            'inicio_inscripciones'     => $command->inicio_inscripciones,
            'titulo_documento1'        => $command->titulo_documento1,
            'documento1'               => $command->documento1,
            'titulo_documento2'        => $command->titulo_documento2,
            'documento2'               => $command->documento2,
            'titulo_documento3'        => $command->titulo_documento3,
            'documento3'               => $command->documento3,
            'titulo_documento4'        => $command->titulo_documento4,
            'documento4'               => $command->documento4,
            'dirigido'                 => $command->dirigido,
            'inversion'                => $command->inversion,
            'requisitos'               => $command->requisitos,
            'creditaje'                => $command->creditaje,
            'objetivo'                 => $command->objetivo,
            'nota'                     => $command->nota,
            'id_tipoprograma'          => $command->id_tipoprograma,
            'url_video'                => $command->url_video,
            'estado'                   => $command->estado,
            'fecha_reg'                => now(),
        ]);
    }
}
