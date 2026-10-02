<?php

namespace App\Application\UsuariosPrograma\Handlers;

use App\Application\UsuariosPrograma\Commands\CreateUsuarioProgramaCommand;
use App\Application\UsuariosPrograma\DTOs\UsuarioProgramaDTO;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;

class CreateUsuarioProgramaHandler
{
    public function __construct(
        private readonly UsuarioProgramaRepositoryInterface $repository,
    ) {}

    public function handle(CreateUsuarioProgramaCommand $command): UsuarioProgramaDTO
    {
        $row = $this->repository->create([
            'id_usuarioprograma'  => $command->id_usuarioprograma,
            'id_us'               => $command->id_us,
            'id_us_reg'           => $command->id_us_reg,
            'num_usuarioprograma' => $command->num_usuarioprograma,
            'id_programa'         => $command->id_programa,
            'id_tipoprograma'     => $command->id_tipoprograma,
            'estado'              => $command->estado,
            'fecha_reg'           => now(),
        ]);

        return UsuarioProgramaDTO::fromRow($row);
    }
}
