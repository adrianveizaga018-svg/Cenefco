<?php

namespace App\Application\UsuariosTipoPrograma\Handlers;

use App\Application\UsuariosTipoPrograma\Commands\UpdateUsuarioTipoProgramaCommand;
use App\Application\UsuariosTipoPrograma\DTOs\UsuarioTipoProgramaDTO;
use App\Domain\UsuariosTipoPrograma\Contracts\UsuarioTipoProgramaRepositoryInterface;

class UpdateUsuarioTipoProgramaHandler
{
    public function __construct(
        private readonly UsuarioTipoProgramaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateUsuarioTipoProgramaCommand $command): UsuarioTipoProgramaDTO
    {
        $data = array_filter([
            'id_tipoprograma' => $command->id_tipoprograma,
            'estado'          => $command->estado,
        ], fn ($v) => $v !== null);

        $row = $this->repository->update($command->id, $data);

        return UsuarioTipoProgramaDTO::fromRow($row);
    }
}
