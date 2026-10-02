<?php

namespace App\Application\Acreditaciones\Handlers;

use App\Application\Acreditaciones\Commands\UpdateAcreditacionCommand;
use App\Application\Acreditaciones\DTOs\AcreditacionDTO;
use App\Domain\Acreditaciones\Contracts\AcreditacionRepositoryInterface;

class UpdateAcreditacionHandler
{
    public function __construct(
        private readonly AcreditacionRepositoryInterface $repository,
    ) {}

    public function handle(UpdateAcreditacionCommand $command): AcreditacionDTO
    {
        $data = array_filter([
            'nombre'            => $command->nombre,
            'entidad_otorgante' => $command->entidad_otorgante,
            'tipo'              => $command->tipo,
            'descripcion'       => $command->descripcion,
            'logo_url'          => $command->logo_url,
            'logo_alt'          => $command->logo_alt,
            'url_verificacion'  => $command->url_verificacion,
            'fecha_obtencion'   => $command->fecha_obtencion,
            'fecha_vencimiento' => $command->fecha_vencimiento,
            'orden'             => $command->orden,
            'activo'            => $command->activo,
        ], fn ($v) => $v !== null);

        return $this->repository->update($command->id, $data);
    }
}
