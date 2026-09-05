<?php

namespace App\Application\UsuariosPlanDoc\Handlers;

use App\Application\UsuariosPlanDoc\Commands\CreateUsuarioPlanDocCommand;
use App\Application\UsuariosPlanDoc\DTOs\UsuarioPlanDocDTO;
use App\Domain\UsuariosPlanDoc\Contracts\UsuarioPlanDocRepositoryInterface;

class CreateUsuarioPlanDocHandler
{
    public function __construct(
        private readonly UsuarioPlanDocRepositoryInterface $repository,
    ) {}

    public function handle(CreateUsuarioPlanDocCommand $command): UsuarioPlanDocDTO
    {
        $row = $this->repository->create([
            'id_usuarioplandoc'  => $command->id_usuarioplandoc,
            'id_us'              => $command->id_us,
            'id_us_reg'          => $command->id_us_reg,
            'num_usuarioplandoc' => $command->num_usuarioplandoc,
            'id_plandoc'         => $command->id_plandoc,
            'estado'             => $command->estado,
            'fecha_reg'          => now(),
        ]);

        return UsuarioPlanDocDTO::fromRow($row);
    }
}
