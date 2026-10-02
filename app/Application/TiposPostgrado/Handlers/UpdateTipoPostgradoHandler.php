<?php

namespace App\Application\TiposPostgrado\Handlers;

use App\Application\TiposPostgrado\Commands\UpdateTipoPostgradoCommand;
use App\Application\TiposPostgrado\DTOs\TipoPostgradoDTO;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;

class UpdateTipoPostgradoHandler
{
    public function __construct(private readonly TipoPostgradoRepositoryInterface $repository) {}

    public function handle(UpdateTipoPostgradoCommand $command): TipoPostgradoDTO
    {
        $this->repository->update($command->idTipopost, array_filter([
            'id_tipopago'       => $command->idTipopago,
            'descuentopostgrado'=> $command->descuentopostgrado,
            'calculo_cuota'     => $command->calculoCuota,
            'estado'            => $command->estado,
        ], fn ($v) => $v !== null));

        return TipoPostgradoDTO::fromRow($this->repository->findById($command->idTipopost));
    }
}
