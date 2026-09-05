<?php

namespace App\Application\FechasDoc\Handlers;

use App\Application\FechasDoc\Commands\CreateFechaDocCommand;
use App\Application\FechasDoc\DTOs\FechaDocDTO;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;

class CreateFechaDocHandler
{
    public function __construct(
        private readonly FechaDocRepositoryInterface $repository
    ) {}

    public function handle(CreateFechaDocCommand $command): FechaDocDTO
    {
        return $this->repository->create([
            'id_fechadoc'    => $command->id_fechadoc,
            'id_plandoc'     => $command->id_plandoc,
            'id_us_reg'      => $command->id_us_reg ?? 0,
            'num_fechadoc'   => $command->num_fechadoc ?? 0,
            'nro_doc'        => $command->nro_doc,
            'tipo_documento' => $command->tipo_documento,
            'fecha_inicio'   => $command->fecha_inicio,
            'fecha_fin'      => $command->fecha_fin,
            'obligatorio'    => $command->obligatorio,
            'estado'         => $command->estado,
            'fecha_reg'      => now(),
        ]);
    }
}
