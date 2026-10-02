<?php

namespace App\Application\TiposPrograma\Handlers;

use App\Application\TiposPrograma\Commands\UpdateTipoProgramaCommand;
use App\Application\TiposPrograma\DTOs\TipoProgramaDTO;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;

class UpdateTipoProgramaHandler
{
    public function __construct(private readonly TipoProgramaRepositoryInterface $repository) {}

    public function handle(UpdateTipoProgramaCommand $command): TipoProgramaDTO
    {
        $this->repository->update($command->idTipoprograma, array_filter([
            'nombre_tipoprograma' => $command->nombreTipoprograma,
            'estado'              => $command->estado,
        ], fn ($v) => $v !== null));

        return TipoProgramaDTO::fromRow($this->repository->findById($command->idTipoprograma));
    }
}
