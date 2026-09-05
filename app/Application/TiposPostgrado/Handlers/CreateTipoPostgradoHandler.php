<?php

namespace App\Application\TiposPostgrado\Handlers;

use App\Application\TiposPostgrado\Commands\CreateTipoPostgradoCommand;
use App\Application\TiposPostgrado\DTOs\TipoPostgradoDTO;
use App\Domain\TiposPostgrado\Contracts\TipoPostgradoRepositoryInterface;

class CreateTipoPostgradoHandler
{
    public function __construct(private readonly TipoPostgradoRepositoryInterface $repository) {}

    public function handle(CreateTipoPostgradoCommand $command): TipoPostgradoDTO
    {
        $row = $this->repository->create([
            'id_tipopost'       => $command->idTipopost,
            'id_plan'           => $command->idPlan,
            'id_us_reg'         => $command->idUsReg,
            'num_tipopost'      => $command->numTipopost,
            'id_tipopago'       => $command->idTipopago,
            'descuentopostgrado'=> $command->descuentopostgrado,
            'calculo_cuota'     => $command->calculoCuota,
            'estado'            => 1,
            'fecha_reg'         => now(),
        ]);

        return TipoPostgradoDTO::fromRow($row);
    }
}
