<?php

namespace App\Application\MdlCourses\Handlers;

use App\Application\MdlCourses\Commands\CreateMdlCourseCommand;
use App\Application\MdlCourses\DTOs\MdlCourseDTO;
use App\Domain\MdlCourses\Contracts\MdlCourseRepositoryInterface;

class CreateMdlCourseHandler
{
    public function __construct(private readonly MdlCourseRepositoryInterface $repository) {}

    public function handle(CreateMdlCourseCommand $command): MdlCourseDTO
    {
        $row = $this->repository->create([
            'id'                      => $command->id,
            'id_us_reg'               => $command->idUsReg,
            'num_modcurso'            => $command->numModcurso ?? 0,
            'fullname'                => $command->fullname,
            'shortname'               => $command->shortname,
            'id_docente'              => $command->idDocente,
            'category'                => $command->category,
            'sigla'                   => $command->sigla,
            'paralelo'                => $command->paralelo,
            'cupo'                    => $command->cupo,
            'grado_academico1'        => $command->gradoAcademico1,
            'grado_academico2'        => $command->gradoAcademico2,
            'observacion_imp'         => $command->observacionImp,
            'imparte_fecha_inicio'    => $command->imparteFechaInicio,
            'imparte_fecha_fin'       => $command->imparteFechaFin,
            'imparte_fecha_acta'      => $command->imparteFechaActa,
            'ocultar_coordinador_acta'=> $command->ocultarCoordinadorActa,
            'id_coordinador'          => $command->idCoordinador,
            'grado_coordinador1'      => $command->gradoCoordinador1,
            'grado_coordinador2'      => $command->gradoCoordinador2,
            'titulo_personalizado'    => $command->tituloPersonalizado,
            'subtitulo_personalizado' => $command->subtituloPersonalizado,
            'gestion'                 => $command->gestion,
            'per_modificar'           => $command->perModificar,
            'fecha_reg'               => now(),
            'estado'                  => 1,
        ]);

        return MdlCourseDTO::fromRow($row);
    }
}
