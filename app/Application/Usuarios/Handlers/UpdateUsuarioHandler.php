<?php

namespace App\Application\Usuarios\Handlers;

use App\Application\Usuarios\Commands\UpdateUsuarioCommand;
use App\Application\Usuarios\DTOs\UserDTO;
use App\Domain\Usuarios\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UpdateUsuarioHandler
{
    public function __construct(
        private readonly UserRepositoryInterface $repository
    ) {}

    public function handle(UpdateUsuarioCommand $command): UserDTO
    {
        $data = [
            'nombre'   => $command->nombre,
            'apellido' => $command->apellido,
            'email'    => $command->email,
        ];

        if ($command->password !== null) {
            $data['password_hash'] = Hash::make($command->password);
        }

        if ($command->tipo !== null) {
            $data['tipo'] = $command->tipo;
        }

        if ($command->rolId !== null) {
            $data['rol_id'] = $command->rolId;
        }

        if ($command->activo !== null) {
            $data['activo'] = $command->activo;
        }

        return $this->repository->update($command->id, $data);
    }
}
