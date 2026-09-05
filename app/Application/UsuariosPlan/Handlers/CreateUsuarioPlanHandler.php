<?php

namespace App\Application\UsuariosPlan\Handlers;

use App\Application\UsuariosPlan\Commands\CreateUsuarioPlanCommand;
use App\Application\UsuariosPlan\DTOs\UsuarioPlanDTO;
use App\Domain\UsuariosPlan\Contracts\UsuarioPlanRepositoryInterface;

class CreateUsuarioPlanHandler
{
    public function __construct(
        private readonly UsuarioPlanRepositoryInterface $repository,
    ) {}

    public function handle(CreateUsuarioPlanCommand $command): UsuarioPlanDTO
    {
        $row = $this->repository->create([
            'id_usuarioplan'  => $command->id_usuarioplan,
            'id_us'           => $command->id_us,
            'id_us_reg'       => $command->id_us_reg,
            'num_usuarioplan' => $command->num_usuarioplan,
            'id_plan'         => $command->id_plan,
            'estado'          => $command->estado,
            'fecha_reg'       => now(),
        ]);

        return UsuarioPlanDTO::fromRow($row);
    }
}
