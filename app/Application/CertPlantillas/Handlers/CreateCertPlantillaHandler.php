<?php

namespace App\Application\CertPlantillas\Handlers;

use App\Application\CertPlantillas\Commands\CreateCertPlantillaCommand;
use App\Application\CertPlantillas\DTOs\CertPlantillaDTO;
use App\Domain\CertPlantillas\Contracts\CertPlantillaRepositoryInterface;

class CreateCertPlantillaHandler
{
    public function __construct(
        private readonly CertPlantillaRepositoryInterface $repository
    ) {}

    public function handle(CreateCertPlantillaCommand $command): CertPlantillaDTO
    {
        $row = $this->repository->create([
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
            'id_us_reg'      => $command->id_us_reg,
            'created_at'     => now(),
        ]);

        return CertPlantillaDTO::fromRow($row);
    }
}
