<?php

namespace App\Application\UsuariosTipoPrograma\Handlers;

use App\Application\UsuariosTipoPrograma\Commands\CreateUsuarioTipoProgramaCommand;
use App\Application\UsuariosTipoPrograma\DTOs\UsuarioTipoProgramaDTO;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;

class CreateUsuarioTipoProgramaHandler
{
    public function __construct(
        private readonly UsuarioTipoProgramaRepositoryInterface $repository,
    ) {}

    public function handle(CreateUsuarioTipoProgramaCommand $command): UsuarioTipoProgramaDTO
    {
        $row = $this->repository->create([
            'id_usuariotipoprograma'  => $command->id_usuariotipoprograma,
            'id_us'                   => $command->id_us,
            'id_us_reg'               => $command->id_us_reg,
            'num_usuariotipoprograma' => $command->num_usuariotipoprograma,
            'id_tipoprograma'         => $command->id_tipoprograma,
            'estado'                  => $command->estado,
            'fecha_reg'               => now(),
        ]);

        return UsuarioTipoProgramaDTO::fromRow($row);
    }
}
