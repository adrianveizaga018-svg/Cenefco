<?php

namespace App\Application\HistoriaMunicipio\Handlers;

use App\Application\HistoriaMunicipio\Commands\CreateHistoriaMunicipioCommand;
use App\Application\HistoriaMunicipio\DTOs\HistoriaMunicipioDTO;
use App\Domain\HistoriaMunicipio\Contracts\HistoriaMunicipioRepositoryInterface;

class CreateHistoriaMunicipioHandler
{
    public function __construct(private readonly HistoriaMunicipioRepositoryInterface $repository) {}

    public function handle(CreateHistoriaMunicipioCommand $command): HistoriaMunicipioDTO
    {
        return $this->repository->create([
            'titulo' => $command->titulo,
            'contenido' => $command->contenido,
            'fecha_inicio' => $command->fecha_inicio,
            'fecha_fin' => $command->fecha_fin,
            'imagen_url' => $command->imagen_url,
            'orden' => $command->orden,
            'activo' => $command->activo,
        ]);
    }
}
