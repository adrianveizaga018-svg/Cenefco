<?php

namespace App\Application\CertPlantillas\Handlers;

use App\Application\CertPlantillas\Commands\UpdateCertPlantillaCommand;
use App\Application\CertPlantillas\DTOs\CertPlantillaDTO;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;

class UpdateCertPlantillaHandler
{
    public function __construct(
        private readonly CertPlantillaRepositoryInterface $repository
    ) {}

    public function handle(UpdateCertPlantillaCommand $command): CertPlantillaDTO
    {
        $changes = array_filter([
            'nombre'         => $command->nombre,
            'tipo'           => $command->tipo,
            'imagen_url'     => $command->imagen_url,
            'ancho_px'       => $command->ancho_px,
            'alto_px'        => $command->alto_px,
            'orientacion'    => $command->orientacion,
            'formato_salida' => $command->formato_salida,
            'calidad_jpg'    => $command->calidad_jpg,
            'fuente_default' => $command->fuente_default,
            'color_default'  => $command->color_default,
            'estado'         => $command->estado,
            'notas'          => $command->notas,
        ], fn ($v) => $v !== null);

        $changes['updated_at'] = now();

        $row = $this->repository->update($command->id, $changes);

        return CertPlantillaDTO::fromRow($row);
    }
}
