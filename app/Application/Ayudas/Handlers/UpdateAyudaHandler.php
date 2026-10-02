<?php

namespace App\Application\Ayudas\Handlers;

use App\Application\Ayudas\Commands\UpdateAyudaCommand;
use App\Application\Ayudas\DTOs\AyudaDTO;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;

class UpdateAyudaHandler
{
    public function __construct(private readonly AyudaRepositoryInterface $repository) {}

    public function handle(UpdateAyudaCommand $command): AyudaDTO
    {
        $this->repository->findById($command->id);

        $changes = array_filter([
            'gestion'               => $command->gestion,
            'monto_pagado'          => $command->monto_pagado,
            'nro_recibo'            => $command->nro_recibo,
            'fecha_recibo'          => $command->fecha_recibo,
            'observacion_pago'      => $command->observacion_pago,
            'id_categoriatipoayuda' => $command->id_categoriatipoayuda,
            'estado'                => $command->estado,
        ], fn ($v) => $v !== null);

        $this->repository->update($command->id, $changes);

        return AyudaDTO::fromRow($this->repository->findById($command->id));
    }
}
