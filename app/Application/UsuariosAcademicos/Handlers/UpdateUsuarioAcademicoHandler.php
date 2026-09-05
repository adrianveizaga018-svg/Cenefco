<?php

namespace App\Application\UsuariosAcademicos\Handlers;

use App\Application\UsuariosAcademicos\Commands\UpdateUsuarioAcademicoCommand;
use App\Application\UsuariosAcademicos\DTOs\UsuarioAcademicoDTO;
use App\Domain\UsuariosAcademicos\Contracts\UsuarioAcademicoRepositoryInterface;

class UpdateUsuarioAcademicoHandler
{
    public function __construct(
        private readonly UsuarioAcademicoRepositoryInterface $repository
    ) {}

    public function handle(UpdateUsuarioAcademicoCommand $command): UsuarioAcademicoDTO
    {
        $data = array_filter([
            'tipoestudiante'  => $command->tipoestudiante,
            'nombre_usuario'  => $command->nombre_usuario,
            'categoria'       => $command->categoria,
            'titulo_academico' => $command->titulo_academico,
            'appaterno'       => $command->appaterno,
            'apmaterno'       => $command->apmaterno,
            'nombre'          => $command->nombre,
            'ci'              => $command->ci,
            'expedido'        => $command->expedido,
            'telefono'        => $command->telefono,
            'celular'         => $command->celular,
            'genero'          => $command->genero,
            'email'           => $command->email,
            'direccion'       => $command->direccion,
            'ciudad'          => $command->ciudad,
            'pais'            => $command->pais,
            'id_universidad'  => $command->id_universidad,
            'id_carrera'      => $command->id_carrera,
            'estado'          => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
