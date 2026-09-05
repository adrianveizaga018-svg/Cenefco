<?php

namespace App\Application\ProgramasAcademicos\Handlers;

use App\Application\ProgramasAcademicos\Commands\UpdateProgramaAcademicoCommand;
use App\Application\ProgramasAcademicos\DTOs\ProgramaAcademicoDTO;
use App\Domain\ProgramasAcademicos\Contracts\ProgramaAcademicoRepositoryInterface;

class UpdateProgramaAcademicoHandler
{
    public function __construct(
        private readonly ProgramaAcademicoRepositoryInterface $repository
    ) {}

    public function handle(UpdateProgramaAcademicoCommand $command): ProgramaAcademicoDTO
    {
        $data = array_filter([
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
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
