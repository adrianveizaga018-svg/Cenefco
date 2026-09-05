<?php

namespace App\Application\TiposPrograma\Handlers;

use App\Application\TiposPrograma\Commands\CreateTipoProgramaCommand;
use App\Application\TiposPrograma\DTOs\TipoProgramaDTO;
use App\Domain\TiposPrograma\Contracts\TipoProgramaRepositoryInterface;

class CreateTipoProgramaHandler
{
    public function __construct(private readonly TipoProgramaRepositoryInterface $repository) {}

    public function handle(CreateTipoProgramaCommand $command): TipoProgramaDTO
    {
        $row = $this->repository->create([
            'id_tipoprograma'     => $command->idTipoprograma,
            'nombre_tipoprograma' => $command->nombreTipoprograma,
            'id_us_reg'           => $command->idUsReg,
            'num_tipoprograma'    => $command->idTipoprograma,
            'estado'              => $command->estado ?? 1,
            'fecha_reg'           => now(),
            'per_modificar'       => 0,
        ]);

        return TipoProgramaDTO::fromRow($row);
    }
}
