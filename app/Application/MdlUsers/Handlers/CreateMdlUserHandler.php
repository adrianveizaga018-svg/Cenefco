<?php

namespace App\Application\MdlUsers\Handlers;

use App\Application\MdlUsers\Commands\CreateMdlUserCommand;
use App\Application\MdlUsers\DTOs\MdlUserDTO;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;

class CreateMdlUserHandler
{
    public function __construct(private readonly MdlUserRepositoryInterface $repository) {}

    public function handle(CreateMdlUserCommand $command): MdlUserDTO
    {
        $row = $this->repository->create([
            'id'             => $command->id,
            'id_us_reg'      => $command->idUsReg,
            'num_modusuario' => $command->numModusuario ?? 0,
            'nombre_usuario' => $command->nombreUsuario,
            'nombre'         => $command->nombre,
            'appaterno'      => $command->appaterno,
            'apmaterno'      => $command->apmaterno,
            'ci'             => $command->ci,
            'expedido'       => $command->expedido,
            'telefono'       => $command->telefono,
            'celular'        => $command->celular,
            'email'          => $command->email,
            'direccion'      => $command->direccion,
            'ciudad'         => $command->ciudad,
            'per_modificar'  => $command->perModificar,
            'fecha_reg'      => now(),
            'estado'         => 1,
        ]);

        return MdlUserDTO::fromRow($row);
    }
}
