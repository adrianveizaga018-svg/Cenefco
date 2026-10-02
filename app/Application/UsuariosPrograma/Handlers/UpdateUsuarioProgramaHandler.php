<?php

namespace App\Application\UsuariosPrograma\Handlers;

use App\Application\UsuariosPrograma\Commands\UpdateUsuarioProgramaCommand;
use App\Application\UsuariosPrograma\DTOs\UsuarioProgramaDTO;
use App\Domain\UsuariosPrograma\Contracts\UsuarioProgramaRepositoryInterface;

class UpdateUsuarioProgramaHandler
{
    public function __construct(
        private readonly UsuarioProgramaRepositoryInterface $repository,
    ) {}

    public function handle(UpdateUsuarioProgramaCommand $command): UsuarioProgramaDTO
    {
        $data = array_filter([
            'id_programa'     => $command->id_programa,
            'id_tipoprograma' => $command->id_tipoprograma,
            'estado'          => $command->estado,
        ], fn ($v) => $v !== null);

        $row = $this->repository->update($command->id, $data);

        return UsuarioProgramaDTO::fromRow($row);
    }
}
