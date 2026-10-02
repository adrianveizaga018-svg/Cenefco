<?php

namespace App\Application\MdlUsers\Handlers;

use App\Application\MdlUsers\Commands\UpdateMdlUserCommand;
use App\Application\MdlUsers\DTOs\MdlUserDTO;
use App\Domain\MdlUsers\Contracts\MdlUserRepositoryInterface;

class UpdateMdlUserHandler
{
    public function __construct(private readonly MdlUserRepositoryInterface $repository) {}

    public function handle(UpdateMdlUserCommand $command): MdlUserDTO
    {
        $this->repository->update($command->id, array_filter([
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
            'estado'         => $command->estado,
            'per_modificar'  => $command->perModificar,
        ], fn ($v) => $v !== null));

        return MdlUserDTO::fromRow($this->repository->findById($command->id));
    }
}
