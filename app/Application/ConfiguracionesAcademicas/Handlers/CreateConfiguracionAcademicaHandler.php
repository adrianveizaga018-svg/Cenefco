<?php

namespace App\Application\ConfiguracionesAcademicas\Handlers;

use App\Application\ConfiguracionesAcademicas\Commands\CreateConfiguracionAcademicaCommand;
use App\Application\ConfiguracionesAcademicas\DTOs\ConfiguracionAcademicaDTO;
use App\Domain\ConfiguracionesAcademicas\Contracts\ConfiguracionAcademicaRepositoryInterface;

class CreateConfiguracionAcademicaHandler
{
    public function __construct(
        private readonly ConfiguracionAcademicaRepositoryInterface $repository,
    ) {}

    public function handle(CreateConfiguracionAcademicaCommand $command): ConfiguracionAcademicaDTO
    {
        $row = $this->repository->create([
            'id_conf'     => $command->idConf,
            'gestion'     => $command->gestion,
            'id_plan'     => $command->idPlan,
            'descripcion' => $command->descripcion,
            'id_us_reg'   => $command->idUsReg,
            'fecha_reg'   => now(),
            'estado'      => 1,
        ]);

        return ConfiguracionAcademicaDTO::fromRow($row);
    }
}
