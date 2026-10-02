<?php

namespace App\Application\Convenios\Handlers;

use App\Application\Convenios\Commands\UpdateConvenioCommand;
use App\Application\Convenios\DTOs\ConvenioDTO;
use App\Domain\Convenios\Contracts\ConvenioRepositoryInterface;

class UpdateConvenioHandler
{
    public function __construct(
        private readonly ConvenioRepositoryInterface $repository,
    ) {}

    public function handle(UpdateConvenioCommand $command): ConvenioDTO
    {
        $data = array_filter([
            'nombre'            => $command->nombre,
            'institucion'       => $command->institucion,
            'tipo'              => $command->tipo,
            'descripcion'       => $command->descripcion,
            'responsable'       => $command->responsable,
            'contacto_email'    => $command->contacto_email,
            'contacto_telefono' => $command->contacto_telefono,
            'fecha_inicio'      => $command->fecha_inicio,
            'fecha_fin'         => $command->fecha_fin,
            'documento_url'     => $command->documento_url,
            'logo_url'          => $command->logo_url,
            'estado'            => $command->estado,
            'orden'             => $command->orden,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
