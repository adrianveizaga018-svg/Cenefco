<?php

namespace App\Application\Ayudas\Handlers;

use App\Application\Ayudas\Commands\CreateAyudaCommand;
use App\Application\Ayudas\DTOs\AyudaDTO;
use App\Domain\Ayudas\Contracts\AyudaRepositoryInterface;

class CreateAyudaHandler
{
    public function __construct(private readonly AyudaRepositoryInterface $repository) {}

    public function handle(CreateAyudaCommand $command): AyudaDTO
    {
        $row = $this->repository->create([
            'id_ayuda'              => $command->id_ayuda,
            'id_us_reg'             => $command->id_us_reg,
            'num_ayuda'             => $command->num_ayuda,
            'id_us'                 => $command->id_us,
            'gestion'               => $command->gestion,
            'monto_pagado'          => $command->monto_pagado,
            'nro_recibo'            => $command->nro_recibo,
            'fecha_recibo'          => $command->fecha_recibo,
            'observacion_pago'      => $command->observacion_pago,
            'id_categoriatipoayuda' => $command->id_categoriatipoayuda,
            'estado'                => $command->estado,
            'fecha_reg'             => now(),
        ]);

        return AyudaDTO::fromRow($row);
    }
}
