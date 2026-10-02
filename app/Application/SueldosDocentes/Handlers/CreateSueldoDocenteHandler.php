<?php

namespace App\Application\SueldosDocentes\Handlers;

use App\Application\SueldosDocentes\Commands\CreateSueldoDocenteCommand;
use App\Application\SueldosDocentes\DTOs\SueldoDocenteDTO;
use App\Domain\SueldosDocentes\Contracts\SueldoDocenteRepositoryInterface;

class CreateSueldoDocenteHandler
{
    public function __construct(
        private readonly SueldoDocenteRepositoryInterface $repository,
    ) {}

    public function handle(CreateSueldoDocenteCommand $command): SueldoDocenteDTO
    {
        return $this->repository->create([
            'id_us'       => $command->id_us,
            'id_imp'      => $command->id_imp,
            'id_programa' => $command->id_programa,
            'concepto'    => $command->concepto,
            'periodo'     => $command->periodo,
            'gestion'     => $command->gestion,
            'monto_total' => $command->monto_total,
            'observacion' => $command->observacion,
            'archivo_pdf' => $command->archivo_pdf,
            'estado'      => 1,
        ]);
    }
}
