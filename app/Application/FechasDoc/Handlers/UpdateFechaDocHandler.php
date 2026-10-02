<?php

namespace App\Application\FechasDoc\Handlers;

use App\Application\FechasDoc\Commands\UpdateFechaDocCommand;
use App\Application\FechasDoc\DTOs\FechaDocDTO;
use App\Domain\FechasDoc\Contracts\FechaDocRepositoryInterface;

class UpdateFechaDocHandler
{
    public function __construct(
        private readonly FechaDocRepositoryInterface $repository
    ) {}

    public function handle(UpdateFechaDocCommand $command): FechaDocDTO
    {
        $data = array_filter([
            'nro_doc'        => $command->nro_doc,
            'tipo_documento' => $command->tipo_documento,
            'fecha_inicio'   => $command->fecha_inicio,
            'fecha_fin'      => $command->fecha_fin,
            'obligatorio'    => $command->obligatorio,
            'estado'         => $command->estado,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
